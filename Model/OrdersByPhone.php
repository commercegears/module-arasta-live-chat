<?php
/**
 * CommerceGears Arasta Live Chat
 *
 * @copyright Copyright (c) CommerceGears (https://commercegears.com/)
 */
declare(strict_types=1);

namespace CommerceGears\ArastaLiveChat\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\InputException;
use CommerceGears\ArastaLiveChat\Api\OrdersByPhoneInterface;

/**
 * `GET /V1/platform/orders/by-phone`: lets Arasta list the orders of a customer it knows only by a verified phone number
 * (WhatsApp). Magento's search criteria cannot filter orders by address telephone, so the addresses are searched here:
 * the last nine digits narrow the rows in SQL, then PhoneMatcher decides. Returns increment ids only; Arasta reads each
 * order through the standard orders API.
 */
class OrdersByPhone implements OrdersByPhoneInterface
{
    /** Address rows read per request before matching (newest orders first). */
    private const CANDIDATES = 200;
    private const MAX_LIMIT = 20;

    public function __construct(
        private readonly ResourceConnection $resource,
        private readonly PhoneMatcher $matcher
    ) {
    }

    public function find(string $phone, int $limit = 5): array
    {
        $tail = $this->matcher->tail($phone);
        if ($tail === '') {
            throw new InputException(__('A phone number needs at least %1 digits.', PhoneMatcher::MIN_DIGITS));
        }
        $limit = max(1, min(self::MAX_LIMIT, $limit));
        $connection = $this->resource->getConnection('sales');
        $select = $connection->select()
            ->from(['a' => $this->resource->getTableName('sales_order_address', 'sales')], ['telephone'])
            ->join(['o' => $this->resource->getTableName('sales_order', 'sales')], 'o.entity_id = a.parent_id', ['increment_id'])
            ->where("REGEXP_REPLACE(a.telephone, '[^0-9]', '') LIKE ?", '%' . $tail)
            ->order(['o.created_at DESC', 'o.entity_id DESC'])
            ->limit(self::CANDIDATES);
        $found = [];
        foreach ($connection->fetchAll($select) as $row) {
            $incrementId = (string) $row['increment_id'];
            if (isset($found[$incrementId]) || !$this->matcher->matches((string) $row['telephone'], $phone)) {
                continue;
            }
            $found[$incrementId] = true;
            if (count($found) >= $limit) {
                break;
            }
        }
        return array_keys($found);
    }
}

<?php
declare(strict_types=1);

namespace Softaware\ArastaLiveChat\Model\Config\Source;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Customer attributes that can hold a phone number (visible text fields stored as text), for the "Telephone Attribute" setting. Magento has no
 * standard phone attribute on the customer, so the merchant chooses theirs (e.g. phone, mobile, telephone).
 */
class CustomerPhoneAttribute implements OptionSourceInterface
{
    /** Text attributes that never hold a phone number (names, ids and Magento's internal account fields). */
    private const EXCLUDED = [
        'email', 'firstname', 'lastname', 'middlename', 'prefix', 'suffix', 'taxvat', 'password_hash', 'rp_token',
        'confirmation', 'created_in', 'default_billing', 'default_shipping', 'group_id', 'store_id', 'website_id',
    ];
    /** Storage types that can hold a phone number as typed (Magento's internal ids are int/static). */
    private const TEXT_BACKENDS = ['varchar', 'text'];

    public function __construct(private readonly CustomerMetadataInterface $customerMetadata)
    {
    }

    /** @return list<array{value: string, label: string}> */
    public function toOptionArray(): array
    {
        $options = [['value' => '', 'label' => (string) __('-- Not used --')]];
        foreach ($this->customerMetadata->getAllAttributesMetadata() as $attribute) {
            $code = (string) $attribute->getAttributeCode();
            // A free-text field the merchant can see and fill in: not an internal account field.
            if ($attribute->getFrontendInput() !== 'text' || !$attribute->isVisible()
                || !in_array((string) $attribute->getBackendType(), self::TEXT_BACKENDS, true)
                || in_array($code, self::EXCLUDED, true)) {
                continue;
            }
            $options[] = ['value' => $code, 'label' => sprintf('%s (%s)', (string) $attribute->getFrontendLabel(), $code)];
        }
        return $options;
    }
}

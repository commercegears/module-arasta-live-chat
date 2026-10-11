<?php
/**
 * CommerceGears Arasta Live Chat
 *
 * @copyright Copyright (c) CommerceGears (https://commercegears.com/)
 */
declare(strict_types=1);

namespace CommerceGears\ArastaLiveChat\Api;

/**
 * the store's own invoice PDF.
 */
interface InvoicePdfInterface
{
    /**
     * @param int $invoiceId
     * @return \CommerceGears\ArastaLiveChat\Api\Data\PdfFileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $invoiceId): Data\PdfFileInterface;
}

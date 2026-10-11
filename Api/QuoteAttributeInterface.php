<?php
/**
 * CommerceGears Arasta Live Chat
 *
 * @copyright Copyright (c) CommerceGears (https://commercegears.com/)
 */
declare(strict_types=1);

namespace CommerceGears\ArastaLiveChat\Api;

/**
 * tags the shopper's active quote with the platform conversation id, so the order placed from it
 * can be linked to the conversation. Called by the widget loader from the storefront with the shopper's
 * session (customer) or the guest's masked quote id.
 */
interface QuoteAttributeInterface
{
    /**
     * @param string $conversationId Platform conversation id (UUID).
     * @param string|null $cartId Masked quote id of a guest cart; ignored for logged-in customers.
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function set(string $conversationId, ?string $cartId = null): bool;
}

<?php
/**
 * CommerceGears Arasta Live Chat
 *
 * @copyright Copyright (c) CommerceGears (https://commercegears.com/)
 */
declare(strict_types=1);

namespace CommerceGears\ArastaLiveChat\Api;

/**
 * orders whose billing or shipping telephone is a given (verified) phone number.
 */
interface OrdersByPhoneInterface
{
    /**
     * Increment ids of the orders whose billing or shipping telephone is the same line as `$phone`, newest first.
     *
     * @param string $phone
     * @param int $limit
     * @return string[]
     */
    public function find(string $phone, int $limit = 5): array;
}

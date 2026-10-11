<?php
/**
 * CommerceGears Arasta Live Chat
 *
 * @copyright Copyright (c) CommerceGears (https://commercegears.com/)
 */
declare(strict_types=1);

namespace CommerceGears\ArastaLiveChat\Api;

/**
 * capability discovery.
 */
interface VersionInterface
{
    /**
     * @return \CommerceGears\ArastaLiveChat\Api\Data\VersionInfoInterface
     */
    public function get(): Data\VersionInfoInterface;
}

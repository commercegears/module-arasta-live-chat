<?php
/**
 * CommerceGears Arasta Live Chat
 *
 * @copyright Copyright (c) CommerceGears (https://commercegears.com/)
 */
declare(strict_types=1);

namespace CommerceGears\ArastaLiveChat\Api\Data;

interface VersionInfoInterface
{
    /**
     * @return string
     */
    public function getModuleVersion(): string;

    /**
     * @return string
     */
    public function getMagentoVersion(): string;

    /**
     * @return string[]
     */
    public function getCapabilities(): array;
}

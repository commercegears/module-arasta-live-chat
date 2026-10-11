<?php
/**
 * CommerceGears Arasta Live Chat
 *
 * @copyright Copyright (c) CommerceGears (https://commercegears.com/)
 */
declare(strict_types=1);

namespace CommerceGears\ArastaLiveChat\Model\Data;

use CommerceGears\ArastaLiveChat\Api\Data\VersionInfoInterface;

class VersionInfo implements VersionInfoInterface
{
    /**
     * @param string[] $capabilities
     */
    public function __construct(
        private readonly string $moduleVersion,
        private readonly string $magentoVersion,
        private readonly array $capabilities
    ) {
    }

    public function getModuleVersion(): string
    {
        return $this->moduleVersion;
    }

    public function getMagentoVersion(): string
    {
        return $this->magentoVersion;
    }

    public function getCapabilities(): array
    {
        return $this->capabilities;
    }
}

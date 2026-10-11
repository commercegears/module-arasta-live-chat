<?php
/**
 * CommerceGears Arasta Live Chat
 *
 * @copyright Copyright (c) CommerceGears (https://commercegears.com/)
 */
declare(strict_types=1);

namespace CommerceGears\ArastaLiveChat\Model;

use Composer\InstalledVersions;
use Magento\Framework\App\ProductMetadataInterface;
use CommerceGears\ArastaLiveChat\Api\Data\VersionInfoInterface;
use CommerceGears\ArastaLiveChat\Api\VersionInterface;
use CommerceGears\ArastaLiveChat\Model\Data\VersionInfoFactory;

/**
 * `GET /V1/platform/version`: module version, Magento version and the capabilities this module adds, used by Arasta
 * for capability discovery. The capability list is part of the contract with Arasta: only add to it.
 */
class Version implements VersionInterface
{
    public const PACKAGE = 'commercegears/module-arasta-live-chat';
    /** Fallback when the package was not installed with Composer (app/code). */
    public const MODULE_VERSION = '2.3.0';
    /** quote_attribute = conversation tagging endpoint, order_link = signed order post, orders_by_phone = 2.2.0. */
    public const CAPABILITIES = ['invoice_pdf', 'signed_identity', 'quote_attribute', 'order_link', 'orders_by_phone'];

    public function __construct(
        private readonly ProductMetadataInterface $productMetadata,
        private readonly VersionInfoFactory $versionInfoFactory
    ) {
    }

    public function get(): VersionInfoInterface
    {
        return $this->versionInfoFactory->create([
            'moduleVersion' => $this->moduleVersion(),
            'magentoVersion' => $this->productMetadata->getVersion(),
            'capabilities' => self::CAPABILITIES,
        ]);
    }

    private function moduleVersion(): string
    {
        try {
            if (class_exists(InstalledVersions::class) && InstalledVersions::isInstalled(self::PACKAGE)) {
                $version = (string) InstalledVersions::getPrettyVersion(self::PACKAGE);
                if (preg_match('/^v?(\d+\.\d+\.\d+)$/', $version, $m) === 1) {
                    return $m[1];
                }
            }
        } catch (\Throwable) {
            // Fall through to the bundled version.
        }
        return self::MODULE_VERSION;
    }
}

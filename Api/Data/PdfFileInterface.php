<?php
/**
 * CommerceGears Arasta Live Chat
 *
 * @copyright Copyright (c) CommerceGears (https://commercegears.com/)
 */
declare(strict_types=1);

namespace CommerceGears\ArastaLiveChat\Api\Data;

interface PdfFileInterface
{
    /**
     * @return string
     */
    public function getFileName(): string;

    /**
     * @return string
     */
    public function getContentType(): string;

    /**
     * Base64-encoded PDF bytes.
     *
     * @return string
     */
    public function getContent(): string;
}

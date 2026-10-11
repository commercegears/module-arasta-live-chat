<?php
/**
 * CommerceGears Arasta Live Chat
 *
 * @copyright Copyright (c) CommerceGears (https://commercegears.com/)
 */
declare(strict_types=1);

namespace CommerceGears\ArastaLiveChat\Model\Data;

use CommerceGears\ArastaLiveChat\Api\Data\PdfFileInterface;

class PdfFile implements PdfFileInterface
{
    public function __construct(
        private readonly string $fileName,
        private readonly string $content,
        private readonly string $contentType = 'application/pdf'
    ) {
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}

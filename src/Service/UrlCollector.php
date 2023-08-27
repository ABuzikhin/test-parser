<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ParsedUrl;

class UrlCollector
{
    /** @var ParsedUrl[] */
    private array $urls = [];

    public function hasUrls(): bool
    {
        return \count($this->urls) > 0;
    }

    /**
     * @return ParsedUrl[]
     */
    public function getUrls(): array
    {
        return $this->urls;
    }

    public function clearUrls(): self
    {
        $this->urls = [];

        return $this;
    }

    public function addUrl(ParsedUrl $url): self
    {
        $this->urls[] = $url;

        return $this;
    }
}
<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Parsers\SiteParserInterface;

class ParsersChain
{
    private array $parsers = [];

    public function addParser(SiteParserInterface $parser): void
    {
        $this->parsers[] = $parser;
    }

    /**
     * @return SiteParserInterface[]
     */
    public function getParsers(): array
    {
        return $this->parsers;
    }

}
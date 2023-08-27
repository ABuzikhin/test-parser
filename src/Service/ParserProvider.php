<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Parsers\SiteParserInterface;

class ParserProvider
{
    public function __construct(
        private readonly ParsersChain $parsersChain
    ) {}
    public function getParserByUrl(string $url): ?SiteParserInterface
    {
        foreach ($this->parsersChain->getParsers() as $parser) {
            if (!$parser->isSupported($url)) {
                continue;
            }

            return $parser;
        }

        return null;
    }
}
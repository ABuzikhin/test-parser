<?php

declare(strict_types=1);

namespace App\Service\Parsers;

use App\Dto\ParsedProductDto;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.parser')]
interface SiteParserInterface
{
    public function isSupported(string $url): bool;
    public function processDto(ParsedProductDto $dto): ParsedProductDto;
}
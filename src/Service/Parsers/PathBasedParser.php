<?php

declare(strict_types=1);

namespace App\Service\Parsers;

use App\Dto\ParsedProductDto;
use App\Exception\DescriptionNotFoundException;
use App\Exception\ImageNotFoundException;
use App\Exception\ParsingException;
use App\Exception\PriceNotFoundException;
use App\Exception\TitleNotFoundException;
use Symfony\Component\DomCrawler\Crawler;

class PathBasedParser
{
    protected string $domain;

    public function isSupported(string $url): bool
    {
        $domain = \parse_url($url, PHP_URL_HOST);

        if (!$domain) {
            return false;
        }

        return \str_ends_with($domain, $this->domain);
    }

    protected function checkAvailability(Crawler $imageEl, Crawler $titleEl, Crawler $descrEl, Crawler $priceEl): void
    {
        if (0 === $imageEl->count()) {
            throw new ImageNotFoundException('Image Path not found in downloaded document.');
        }

        if (0 === $titleEl->count()) {
            throw new TitleNotFoundException('Title Path not found in downloaded document.');
        }

        if (0 === $descrEl->count()) {
            throw new DescriptionNotFoundException('Description Path not found in downloaded document.');
        }

        if (0 === $priceEl->count()) {
            throw new PriceNotFoundException('Price Path not found in downloaded document.');
        }
    }

    /**
     * @param Crawler $imageEl
     * @param Crawler $titleEl
     * @param Crawler $descrEl
     * @param Crawler $priceEl
     * @param ParsedProductDto $dto
     *
     * @return ParsedProductDto
     */
    protected function processElements(
        Crawler $imageEl,
        Crawler $titleEl,
        Crawler $descrEl,
        Crawler $priceEl,
        ParsedProductDto $dto
    ): ParsedProductDto {
        $this->checkAvailability($imageEl, $titleEl, $descrEl, $priceEl);

        try {
            $dto
                ->setTitle($titleEl->text())
                ->setImageUrl($imageEl->attr('src'))
                ->setDescription($descrEl->text())
                ->setPrice($this->processPrice($priceEl->text()))
            ;
        } catch (\Exception $exception) {
            throw new ParsingException($exception->getMessage());
        }

        return $dto;
    }

    protected function processPrice(string $text): float
    {
        return \intval(
                \preg_replace(
                    '/[^\d]/m',
                    '',
                    \htmlspecialchars_decode($text)
                )
            ) + 0.00;
    }
}
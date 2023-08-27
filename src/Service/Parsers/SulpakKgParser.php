<?php

declare(strict_types=1);

namespace App\Service\Parsers;

use App\Dto\ParsedProductDto;
use Symfony\Component\DomCrawler\Crawler;

class SulpakKgParser extends PathBasedParser implements SiteParserInterface
{
    protected string $domain = 'sulpak.kg';
    private string $imageCssPath = '.swiper-wrapper > a.swiper-slide.swiper-slide-active > picture';
    private string $titleCssPath = 'body > main > div.container > div.title__block.title__block-tablet.flex__block > h1';
    private string $descrCssPath = '#rewiew-tab-container > div.product__main.product__page-block-separator > div > div > div.product__main-part-1 > div.product__main-description > div > p.main-description__text';
    private string $priceCssPath = '#rewiew-tab-container > div.product__main.product__page-block-separator > div > div > div.product__main-part-2 > div.product__main-info-block > div.product__main-info-item.product__main-price-buy.product__main-price-buy-js.product__main-price-buy.super-price > div.product__main-price-wrapper.super-price > div.product__price';

    public function processDto(ParsedProductDto $dto): ParsedProductDto
    {
        $crawler = new Crawler($dto->getResponseHtml());
        $titleEl = $crawler->filter($this->titleCssPath);
        $descrEl = $crawler->filter($this->descrCssPath);
        $priceEl = $crawler->filter($this->priceCssPath);
        $imageEl = $crawler->filter($this->imageCssPath)->filter('img');

        return $this->processElements($imageEl, $titleEl, $descrEl, $priceEl, $dto);
    }
}
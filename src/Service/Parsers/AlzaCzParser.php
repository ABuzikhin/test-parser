<?php

declare(strict_types=1);

namespace App\Service\Parsers;

use App\Dto\ParsedProductDto;
use Symfony\Component\DomCrawler\Crawler;

class AlzaCzParser extends PathBasedParser implements SiteParserInterface
{
    protected string $domain = 'alza.cz';
    private string $imageXpath = '//*[@id="detailPicture"]/div[1]/div/div/div[1]/div[1]/div/swiper-container/swiper-slide[1]/div/img';
    private string $titleXpath = '//*[@id="h1c"]/h1';
    private string $descrXpath = '//*[@id="detailText"]/div[1]/span';
    private string $priceXPath = '//*[@id="detailText"]//div[contains(@class, "price-box__prices")]//span[contains(@class, "price-box__price")]';

    public function processDto(ParsedProductDto $dto): ParsedProductDto
    {
        $crawler = new Crawler($dto->getResponseHtml());
        $titleEl = $crawler->filterXPath($this->titleXpath);
        $descrEl = $crawler->filterXPath($this->descrXpath);
        $priceEl = $crawler->filterXPath($this->priceXPath);
        $imageEl = $crawler->filterXPath($this->imageXpath);

        return $this->processElements($imageEl, $titleEl, $descrEl, $priceEl, $dto);
    }
}
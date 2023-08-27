<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\ParsedProductDto;
use App\Entity\ParsedUrl;
use App\Entity\Product;
use App\Exception\ParsingException;
use App\Service\Parsers\SiteParserInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;

class ParsingService
{
    public function __construct(
        private readonly Browser $browser,
        private readonly ParserProvider $parserProvider,
        private readonly EntityManagerInterface $entityManager,
        private readonly ProductService $productService,
        private readonly SerializerInterface $serializer,
    ) {

    }

    public function parse(ParsedUrl $parsedUrl)
    {
        $url        = $parsedUrl->getUrl();
        $productDto = (new ParsedProductDto())
            ->setSrcUrl($url);

        if (!$this->downloadHtml($parsedUrl, $productDto)) {
            return;
        }

        $parser = $this->parserProvider->getParserByUrl($url);

        if (!$parser instanceof SiteParserInterface) {
            $parsedUrl
                ->markError('No parsers found for URL.')
            ;

            $this->entityManager->flush();

            return;
        }

        try {
            $dto = $parser->processDto($productDto);
        } catch (ParsingException $exception) {
            $parsedUrl->markError($exception->getMessage());
            $this->entityManager->flush();

            return;
        }

        $parsedUrl->setParsedResult($this->serializer->toArray($dto));

        $product = $this->makeProductFromDto($dto, $parsedUrl);

        $parsedUrl->markParsed();
        $this->entityManager->flush();
    }

    protected function makeProductFromDto(ParsedProductDto $dto, ParsedUrl $parsedUrl): Product
    {
        $product = $this->productService->makeProductFromDto($dto);

        $parsedUrl->setProduct($product);

        $this->entityManager->flush();

        return $product;
    }

    private function getPageContent(ParsedUrl $parsedUrl): mixed
    {
        $result = $this->browser->getPageContent($parsedUrl->getUrl());

        if (null !== $result) {
            return $result;
        }

        $parsedUrl->markError('Cannot download html for parse.');
        $this->entityManager->flush();

        return null;
    }

    private function downloadHtml(ParsedUrl $parsedUrl, ParsedProductDto $productDto): bool
    {
        try {
            $responseHtml = $this->getPageContent($parsedUrl);

            if (null === $responseHtml) {
                return false;
            }

            $parsedUrl->setResponseHtml($responseHtml);
            $productDto->setResponseHtml($responseHtml);

            $this->entityManager->flush();

            return true;
        } catch (\Exception $exception) {
            $parsedUrl->markError($exception->getTraceAsString());

            $this->entityManager->flush();

            return false;
        }
    }
}
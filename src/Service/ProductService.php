<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\ParsedProductDto;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function makeProductFromDto(ParsedProductDto $dto): Product
    {
        $product = new Product();

        $product
            ->setTitle($dto->getTitle())
            ->setPrice($dto->getPrice())
            ->setDescription($dto->getDescription())
        ;

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $this->updateImage($product, $dto);
        $this->entityManager->flush();

        return $product;
    }

    public function updateProductFromDto(Product $product, ParsedProductDto $dto): Product
    {
        $product
            ->setTitle($dto->getTitle())
            ->setPrice($dto->getPrice())
            ->setDescription($dto->getDescription())
        ;

        $this->entityManager->flush();

        $this->updateImage($product, $dto);
        $this->entityManager->flush();

        return $product;
    }


    private function updateImage(Product $product, ParsedProductDto $dto): void
    {
        if (null === parse_url($dto->getImageUrl(), PHP_URL_HOST)) {
            return;
        }

        $imageUrl = $dto->getImageUrl();

        $temp     = explode('.', $imageUrl);
        $imageExt = $temp[\count($temp) - 1];

        $imageName = $product->getId() . '.' . $imageExt;

        $src = fopen($dto->getImageUrl(), 'r');
        $dst = fopen('gaufrette://productFs/' . $imageName, 'w');
        $res = stream_copy_to_stream($src, $dst);

        if (false === $res) {
            return;
        }

        $product->setImage($imageName);
    }
}
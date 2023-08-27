<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Product;
use JMS\Serializer\Annotation\Exclude;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParsedProductDto
{
    #[NotBlank]
    private string $title = '';

    #[NotBlank]
    private string $description = '';

    #[GreaterThan(value: 0.00)]
    private float $price = 0.00;

    #[NotBlank]
    private string $imageUrl = '';

    #[Exclude]
    private string $srcUrl = '';

    #[Exclude]
    private string $responseHtml = '';

    public static function createFromProduct(Product $product): self
    {
        $dto = new self();
        $dto
            ->setPrice($product->getPrice())
            ->setDescription($product->getDescription())
            ->setTitle($product->getTitle())
            ->setImageUrl($product->getImage())
            ;

        return $dto;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getSrcUrl(): string
    {
        return $this->srcUrl;
    }

    public function setSrcUrl(string $srcUrl): self
    {
        $this->srcUrl = $srcUrl;

        return $this;
    }


    public function getResponseHtml(): string
    {
        return $this->responseHtml;
    }

    public function setResponseHtml(string $responseHtml): self
    {
        $this->responseHtml = $responseHtml;

        return $this;
    }
}
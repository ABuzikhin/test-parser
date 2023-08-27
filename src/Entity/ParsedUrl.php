<?php

namespace App\Entity;

use App\Repository\ParsedUrlRepository;
use App\Validator\SupportedParsers;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: ParsedUrlRepository::class)]
class ParsedUrl
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 4096)]
    #[NotBlank]
    #[SupportedParsers]
    private ?string $url = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $responseHtml = null;

    #[ORM\Column(type: Types::SMALLINT, enumType: ParsedStatus::class)]
    private ?ParsedStatus $status;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateTime = null;

    #[ORM\ManyToOne(inversedBy: 'parsedUrls')]
    private ?Product $product = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $lastError = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $parsedResult = null;

    public function __construct()
    {
        $this->status   = ParsedStatus::PENDING;
        $this->dateTime = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getResponseHtml(): ?string
    {
        return $this->responseHtml;
    }

    public function setResponseHtml(?string $responseHtml): static
    {
        $this->responseHtml = $responseHtml;

        return $this;
    }

    public function getStatus(): ?ParsedStatus
    {
        return $this->status;
    }

    public function setStatus(ParsedStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): static
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function setLastError(?string $error): static
    {
        $this->lastError = $error;

        return $this;
    }

    public function markError(string $error): static
    {
        $this->lastError = $error;
        $this->status    = ParsedStatus::ERROR;

        return $this;
    }

    public function markParsed(): static
    {
        $this->status    = ParsedStatus::PARSED;
        $this->lastError = null;

        return $this;
    }

    public function getParsedResult(): ?array
    {
        return $this->parsedResult;
    }

    public function setParsedResult(?array $parsedResult): self
    {
        $this->parsedResult = $parsedResult;

        return $this;
    }
}

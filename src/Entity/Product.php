<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ParsedUrl::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $parsedUrls;

    public function __construct()
    {
        $this->parsedUrls = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, ParsedUrl>
     */
    public function getParsedUrls(): Collection
    {
        return $this->parsedUrls;
    }

    public function addParsedUrl(ParsedUrl $parsedUrl): static
    {
        if (!$this->parsedUrls->contains($parsedUrl)) {
            $this->parsedUrls->add($parsedUrl);
            $parsedUrl->setProduct($this);
        }

        return $this;
    }

    public function removeParsedUrl(ParsedUrl $parsedUrl): static
    {
        if ($this->parsedUrls->removeElement($parsedUrl)) {
            // set the owning side to null (unless already changed)
            if ($parsedUrl->getProduct() === $this) {
                $parsedUrl->setProduct(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity]
class Product implements JsonSerializable
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $name = null;

  #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
  private ?string $price = null;

  #[ORM\ManyToOne(inversedBy: 'products')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Category $category = null;

  public function getId(): ?int { return $this->id; }

  public function getName(): ?string { return $this->name; }
  public function setName(string $name): static { $this->name = $name; return $this; }

  public function getPrice(): ?string { return $this->price; }
  public function setPrice(string $price): static { $this->price = $price; return $this; }

  public function getCategory(): ?Category { return $this->category; }
  public function setCategory(?Category $category): static { $this->category = $category; return $this; }

  public function jsonSerialize(): mixed
  {
    return [
      'id'       => $this->id,
      'name'     => $this->name,
      'price'    => $this->price,
      'category' => $this->category,
    ];
  }
}

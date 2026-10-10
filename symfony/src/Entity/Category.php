<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity]
class Category implements JsonSerializable
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $name = null;

  #[ORM\Column(type: 'text', nullable: true)]
  private ?string $description = null;

  #[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class)]
  private Collection $products;

  public function __construct()
  {
    $this->products = new ArrayCollection();
  }

  public function getId(): ?int { return $this->id; }

  public function getName(): ?string { return $this->name; }
  public function setName(string $name): static { $this->name = $name; return $this; }

  public function getDescription(): ?string { return $this->description; }
  public function setDescription(?string $description): static { $this->description = $description; return $this; }

  public function getProducts(): Collection { return $this->products; }

  public function jsonSerialize(): mixed
  {
    return [
      'id'          => $this->id,
      'name'        => $this->name,
      'description' => $this->description,
    ];
  }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Сутність Продукту
 */
#[ORM\Entity]
class Product implements JsonSerializable
{
  /**
   * Id
   * @var int|null
   */
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  /**
   * Yfpdf
   * @var string|null
   */
  #[ORM\Column(length: 255)]
  private ?string $name = null;

  /**
   * Ціна
   * @var string|null
   */
  #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
  private ?string $price = null;

  /**
   * Категорія
   * @var Category|null
   */
  #[ORM\ManyToOne(inversedBy: 'products')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Category $category = null;

  /**
   * Отримати id
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * Отримати назву
   * @return string|null
   */
  public function getName(): ?string
  {
    return $this->name;
  }

  /**
   * Встановити назву
   * @param string $name
   * @return $this
   */
  public function setName(string $name): static
  {
    $this->name = $name;
    return $this;
  }

  /**
   * Отримати ціну
   * @return string|null
   */
  public function getPrice(): ?string
  {
    return $this->price;
  }

  /**
   * Встановити ціну
   * @param string $price
   * @return $this
   */
  public function setPrice(string $price): static
  {
    $this->price = $price;
    return $this;
  }

  /**
   * Отримати категорію
   * @return Category|null
   */
  public function getCategory(): ?Category
  {
    return $this->category;
  }

  /**
   * Встановити категорію
   * @param Category|null $category
   * @return $this
   */
  public function setCategory(?Category $category): static
  {
    $this->category = $category; return $this;
  }

  /**
   * Серіалізація сутності в JSON
   * @return mixed
   */
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

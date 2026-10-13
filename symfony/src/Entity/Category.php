<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Сутність Категорії
 */
#[ORM\Entity]
class Category implements JsonSerializable
{
  /**
   * Унікальний ідентифікатор категорії
   * @var int|null
   */
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  /**
   * Назва категорії
   * @var string|null
   */
  #[ORM\Column(length: 255)]
  private ?string $name = null;

  /**
   * @var string|null Опис категорії
   */
  #[ORM\Column(type: 'text', nullable: true)]
  private ?string $description = null;

  /**
   * Список товарів, що належать до цієї категорії
   * @var Collection|ArrayCollection
   */
  #[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class)]
  private Collection $products;


  public function __construct()
  {
    $this->products = new ArrayCollection();
  }

  /**
   * Отримати id
   * @return int|null
   */
  public function getId(): ?int {
    return $this->id;
  }

  /**
   * Отримати назву
   * @return string|null
   */
  public function getName(): ?string {
    return $this->name;
  }

  /**
   * Встановити назву
   * @param string $name
   * @return $this
   */
  public function setName(string $name): static
  {
    $this->name = $name; return $this;
  }

  /**
   * Отримати опис
   * @return string|null
   */
  public function getDescription(): ?string
  {
    return $this->description;
  }

  /**
   * Встановити опис
   * @param string|null $description
   * @return $this
   */
  public function setDescription(?string $description): static
  {
    $this->description = $description;
    return $this;
  }

  /**
   * Отримати продукти
   * @return Collection
   */
  public function getProducts(): Collection
  {
    return $this->products;
  }

  /**
   * Серіалізація сутності в JSON
   * @return mixed
   */
  public function jsonSerialize(): mixed
  {
    return [
      'id'          => $this->id,
      'name'        => $this->name,
      'description' => $this->description,
    ];
  }
}

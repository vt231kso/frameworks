<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Сутність Клієнта
 */
#[ORM\Entity]
class Client implements JsonSerializable
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
   * Назва
   * @var string|null
   */
  #[ORM\Column(length: 255)]
  private ?string $name = null;

  /**
   * Номер телефону
   * @var string|null
   */
  #[ORM\Column(length: 50)]
  private ?string $phone = null;

  /**
   * email
   * @var string|null
   */
  #[ORM\Column(length: 255)]
  private ?string $email = null;

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
   * Отримати номер
   * @return string|null
   */
  public function getPhone(): ?string
  {
    return $this->phone;
  }

  /**
   * Встановити номер
   * @param string $phone
   * @return $this
   */
  public function setPhone(string $phone): static
  {
    $this->phone = $phone;
    return $this;
  }

  /**
   * Отримати email
   * @return string|null
   */
  public function getEmail(): ?string
  {
    return $this->email;
  }

  /**
   * Встановити email
   * @param string $email
   * @return $this
   */
  public function setEmail(string $email): static
  {
    $this->email = $email;
    return $this;
  }

  /**
   * Серіалізація сутності в JSON
   * @return mixed
   */
  public function jsonSerialize(): mixed
  {
    return [
      'id'    => $this->id,
      'name'  => $this->name,
      'phone' => $this->phone,
      'email' => $this->email,
    ];
  }
}

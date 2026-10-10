<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity]
class Client implements JsonSerializable
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $name = null;

  #[ORM\Column(length: 50)]
  private ?string $phone = null;

  #[ORM\Column(length: 255)]
  private ?string $email = null;

  public function getId(): ?int { return $this->id; }

  public function getName(): ?string { return $this->name; }
  public function setName(string $name): static { $this->name = $name; return $this; }

  public function getPhone(): ?string { return $this->phone; }
  public function setPhone(string $phone): static { $this->phone = $phone; return $this; }

  public function getEmail(): ?string { return $this->email; }
  public function setEmail(string $email): static { $this->email = $email; return $this; }

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

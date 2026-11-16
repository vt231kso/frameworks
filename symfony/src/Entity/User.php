<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Цей email уже зареєстровано')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 180, unique: true)]
  #[Assert\NotBlank, Assert\Email]
  private ?string $email = null;

  #[ORM\Column]
  private array $roles = [];

  #[ORM\Column]
  private ?string $password = null;

  /**
   * @return int|null
   */
  public function getId(): ?int { return $this->id; }

  /**
   * @return string|null
   */
  public function getEmail(): ?string { return $this->email; }

  /**
   * @param string $email
   * @return $this
   */
  public function setEmail(string $email): static { $this->email = $email; return $this; }

  /**
   * @return string
   */
  public function getUserIdentifier(): string { return (string) $this->email; }

  /**
   * @return array|string[]
   */
  public function getRoles(): array
  {
    $roles = $this->roles;
    $roles[] = 'ROLE_CLIENT';
    return array_values(array_unique($roles));
  }

  /**
   * @param array $roles
   * @return $this
   */
  public function setRoles(array $roles): static { $this->roles = $roles; return $this; }

  /**
   * @return string|null
   */
  public function getPassword(): ?string { return $this->password; }

  /**
   * @param string $hash
   * @return $this
   */
  public function setPassword(string $hash): static { $this->password = $hash; return $this; }

  /**
   * @return void
   */
  public function eraseCredentials(): void {}
}


<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`order`')]
class Order
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $deliveryAddress = null;

  #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
  private ?string $totalAmount = null;

  #[ORM\Column(length: 50, nullable: true)]
  private ?string $status = null;

  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: false)]
  private ?Client $client = null;

  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: true)]
  private ?Courier $courier = null;

  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: false)]
  private ?Product $product = null;

  public function getId(): ?int { return $this->id; }
  public function getDeliveryAddress(): ?string { return $this->deliveryAddress; }
  public function setDeliveryAddress(string $deliveryAddress): self { $this->deliveryAddress = $deliveryAddress; return $this; }
  public function getTotalAmount(): ?string { return $this->totalAmount; }
  public function setTotalAmount(string $totalAmount): self { $this->totalAmount = $totalAmount; return $this; }
  public function getStatus(): ?string { return $this->status; }
  public function setStatus(?string $status): self { $this->status = $status; return $this; }
  public function getClient(): ?Client { return $this->client; }
  public function setClient(?Client $client): self { $this->client = $client; return $this; }
  public function getCourier(): ?Courier { return $this->courier; }
  public function setCourier(?Courier $courier): self { $this->courier = $courier; return $this; }
  public function getProduct(): ?Product { return $this->product; }
  public function setProduct(?Product $product): self { $this->product = $product; return $this; }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity]
#[ORM\Table(name: '`order`')]
class Order implements JsonSerializable
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
  public function setDeliveryAddress(string $deliveryAddress): static {$this->deliveryAddress = $deliveryAddress; return$this; }

  public function getTotalAmount(): ?string { return $this->totalAmount; }
  public function setTotalAmount(string $totalAmount): static {$this->totalAmount = $totalAmount; return$this; }

  public function getStatus(): ?string { return $this->status; }
  public function setStatus(?string $status): static {$this->status = $status; return$this; }

  public function getClient(): ?Client { return $this->client; }
  public function setClient(?Client $client): static {$this->client = $client; return$this; }

  public function getCourier(): ?Courier { return $this->courier; }
  public function setCourier(?Courier $courier): static {$this->courier = $courier; return$this; }

  public function getProduct(): ?Product { return $this->product; }
  public function setProduct(?Product $product): static {$this->product = $product; return$this; }

  public function jsonSerialize(): mixed
  {
    return [
      'id'               => $this->id,
      'delivery_address' => $this->deliveryAddress,
      'total_amount'     => $this->totalAmount,
      'status'           => $this->status,
      'client'           => $this->client,
      'courier'          => $this->courier,
      'product'          => $this->product,
    ];
  }
}

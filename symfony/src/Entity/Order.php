<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Сутність Замовлення
 */
#[ORM\Entity]
#[ORM\Table(name: '`order`')]
class Order implements JsonSerializable
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
   * Адреса доставки
   * @var string|null
   */
  #[ORM\Column(length: 255)]
  private ?string $deliveryAddress = null;
  /**
   * Сума
   * @var string|null
   */
  #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
  private ?string $totalAmount = null;

  /**
   * Статус
   * @var string|null
   */
  #[ORM\Column(length: 50, nullable: true)]
  private ?string $status = null;

  /**
   * Клієнт
   * @var Client|null
   */
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: false)]
  private ?Client $client = null;
  /**
   * Курєр
   * @var Courier|null
   */
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: true)]
  private ?Courier $courier = null;
  /**
   * Продукт
   * @var Product|null
   */
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: false)]
  private ?Product $product = null;

  /**
   * Отримати id
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * Отримати адресу доставки
   * @return string|null
   */
  public function getDeliveryAddress(): ?string
  {
    return $this->deliveryAddress;
  }

  /**
   * Встановити адресу доставки
   * @param string $deliveryAddress
   * @return $this
   */
  public function setDeliveryAddress(string $deliveryAddress): static
  {
    $this->deliveryAddress = $deliveryAddress;
    return$this;
  }

  /**
   * Встановити псуму
   * @return string|null
   */
  public function getTotalAmount(): ?string
  {
    return $this->totalAmount;
  }

  /**
   * Отримати суму
   * @param string $totalAmount
   * @return $this
   */
  public function setTotalAmount(string $totalAmount): static
  {
    $this->totalAmount = $totalAmount;
    return$this;
  }

  /**
   * Отримати статус
   * @return string|null
   */
  public function getStatus(): ?string
  {
    return $this->status;
  }

  /**
   * Встановити статус
   * @param string|null $status
   * @return $this
   */
  public function setStatus(?string $status): static
  {
    $this->status = $status;
    return$this;
  }

  /**
   * Отримати клієнта
   * @return Client|null
   */
  public function getClient(): ?Client
  {
    return $this->client;
  }

  /**
   * Встановити клієнта
   * @param Client|null $client
   * @return $this
   */
  public function setClient(?Client $client): static
  {
    $this->client = $client;
    return$this;
  }

  /**
   * Отримати курєра
   * @return Courier|null
   */
  public function getCourier(): ?Courier
  {
    return $this->courier;
  }

  /**
   * Встановити курєра
   * @param Courier|null $courier
   * @return $this
   */
  public function setCourier(?Courier $courier): static
  {
    $this->courier = $courier;
    return$this;
  }

  /**
   * Отримати продукт
   * @return Product|null
   */
  public function getProduct(): ?Product
  {
    return $this->product;
  }

  /**
   * Встановити продукт
   * @param Product|null $product
   * @return $this
   */
  public function setProduct(?Product $product): static
  {
    $this->product = $product;
    return$this;
  }

  /**
   * Серіалізація сутності в JSON
   * @return mixed
   */
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

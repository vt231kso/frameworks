<?php

namespace App\Repository;
use App\Entity\Order;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends BaseRepository<Order>
 */
class OrderRepository extends BaseRepository
{
  public const LIKE_FIELDS = ['delivery_address'];
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Order::class);
  }
}

<?php

namespace App\Repository;

use App\Entity\Courier;
use Doctrine\Persistence\ManagerRegistry;



/**
 * @extends BaseRepository<Courier>
 */
class CourierRepository extends BaseRepository
{
  public const LIKE_FIELDS = ['name', 'phone'];
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Courier::class);
  }
}

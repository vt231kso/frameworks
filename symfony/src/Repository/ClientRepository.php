<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends BaseRepository<Client>
 */
class ClientRepository extends BaseRepository
{
  public const LIKE_FIELDS = ['name', 'email', 'phone'];
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Client::class);
  }
}

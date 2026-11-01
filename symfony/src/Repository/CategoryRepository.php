<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends BaseRepository<Category>
 */
class CategoryRepository  extends BaseRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Category::class);
  }
}

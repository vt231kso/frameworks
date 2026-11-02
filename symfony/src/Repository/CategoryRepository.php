<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends BaseRepository<Category>
 */
class CategoryRepository  extends BaseRepository
{

  public const LIKE_FIELDS = ['name', 'description'];
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Category::class);
  }
}

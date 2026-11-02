<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * @template T of object
 * @extends ServiceEntityRepository<T>
 */
abstract class BaseRepository extends ServiceEntityRepository
{
  public const LIKE_FIELDS = [];

  /**
   * @throws \InvalidArgumentException Якщо передано недозволений query-параметр
   */
  public function filterAndPaginate(
    Request $request,
    array $allowedFields,
    ?array $likeFields = null
  ): array {
    if ($likeFields === null) {
      $likeFields = static::LIKE_FIELDS;
    }

    $allQueryParams = array_keys($request->query->all());
    $systemParams = ['page', 'itemsPerPage'];

    foreach ($allQueryParams as $queryParam) {
      if (in_array($queryParam, $systemParams, true)) {
        continue;
      }

      $cleanParam = preg_replace('/_(from|to)$/', '', $queryParam);

      if (!in_array($queryParam, $allowedFields, true) && !in_array($cleanParam, $allowedFields, true)) {
        throw new \InvalidArgumentException("Invalid query parameter: '{$queryParam}'");
      }
    }

    $qb = $this->createQueryBuilder('e');

    foreach ($allowedFields as $field) {
      if (in_array($field, $systemParams, true)) {
        continue;
      }

      $propertyName = $field;

      // category_id -> category (назва властивості в сутності)
      if (str_ends_with($field, '_id')) {
        $propertyName = substr($field, 0, -3);
      }

      // delivery_address -> deliveryAddress
      $targetField = $this->snakeToCamel($propertyName);

      $val = $request->query->get($field);
      $fromVal = $request->query->get($field . '_from');
      $toVal = $request->query->get($field . '_to');

      if (($val === null || $val === '') && ($fromVal === null || $fromVal === '') && ($toVal === null || $toVal === '')) {
        continue;
      }

      if ($val !== null && $val !== '') {
        if (in_array($field, $likeFields, true)) {
          $qb->andWhere("e.{$targetField} LIKE :{$field}")
            ->setParameter($field, '%' . $val . '%');
        } else {
          $qb->andWhere("e.{$targetField} = :{$field}")
            ->setParameter($field, $val);
        }
      }

      if ($fromVal !== null && $fromVal !== '') {
        $qb->andWhere("e.{$targetField} >= :{$field}_from")
          ->setParameter($field . '_from', $fromVal);
      }

      if ($toVal !== null && $toVal !== '') {
        $qb->andWhere("e.{$targetField} <= :{$field}_to")
          ->setParameter($field . '_to', $toVal);
      }
    }

    $itemsPerPage = max(1, min($request->query->getInt('itemsPerPage', 10), 100));
    $page = max(1, $request->query->getInt('page', 1));
    $firstResult = ($page - 1) * $itemsPerPage;

    return $qb->setFirstResult($firstResult)
      ->setMaxResults($itemsPerPage)
      ->getQuery()
      ->getResult();
  }

  private function snakeToCamel(string $input): string
  {
    return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
  }
}

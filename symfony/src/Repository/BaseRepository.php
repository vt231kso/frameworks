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
    $joinedAssociations = [];
    $metadata = $this->getClassMetadata();

    foreach ($allowedFields as $field) {
      if (in_array($field, ['page', 'itemsPerPage'], true)) {
        continue;
      }

      $queryKey = $field;
      $propertyName = $field;

      if (str_ends_with($field, '_id')) {
        $propertyName = substr($field, 0, -3);
      }

      $camelProperty = $this->snakeToCamel($propertyName);

      $val = $request->query->get($queryKey);
      $fromVal = $request->query->get($queryKey . '_from');
      $toVal = $request->query->get($queryKey . '_to');

      if (($val === null || $val === '') && ($fromVal === null || $fromVal === '') && ($toVal === null || $toVal === '')) {
        continue;
      }

      $targetAlias = 'e';
      $targetField = $camelProperty;


      if ($metadata->hasAssociation($camelProperty)) {
        if (!isset($joinedAssociations[$camelProperty])) {
          $joinAlias = $camelProperty . '_join';
          $qb->leftJoin("e.{$camelProperty}", $joinAlias);
          $joinedAssociations[$camelProperty] = $joinAlias;
        }
        $targetAlias = $joinedAssociations[$camelProperty];
        $targetField = 'id';
      }


      if ($val !== null && $val !== '') {
        $paramName = str_replace('.', '_', $field);

        if (in_array($field, $likeFields, true)) {
          $qb->andWhere("{$targetAlias}.{$targetField} LIKE :{$paramName}")
            ->setParameter($paramName, '%' . $val . '%');
        } else {
          $qb->andWhere("{$targetAlias}.{$targetField} = :{$paramName}")
            ->setParameter($paramName, $val);
        }
      }

      if ($fromVal !== null && $fromVal !== '') {
        $paramFrom = str_replace('.', '_', $field) . '_from';
        $qb->andWhere("{$targetAlias}.{$targetField} >= :{$paramFrom}")
          ->setParameter($paramFrom, $fromVal);
      }


      if ($toVal !== null && $toVal !== '') {
        $paramTo = str_replace('.', '_', $field) . '_to';
        $qb->andWhere("{$targetAlias}.{$targetField} <= :{$paramTo}")
          ->setParameter($paramTo, $toVal);
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

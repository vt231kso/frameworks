<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
  private array $products = [
    1 => ['id' => 1, 'title' => 'Ноутбук', 'price' => 25000],
    2 => ['id' => 2, 'title' => 'Мишка', 'price' => 500],
  ];
  #[Route('/products', name: 'get_products', methods: ['GET'])]
  public function getProducts(): JsonResponse
  {
    return new JsonResponse(array_values($this->products), Response::HTTP_OK);
  }

  #[Route('/products/{id}', name: 'get_product_item', methods: ['GET'])]
  public function getProductItem(int $id): JsonResponse
  {
    if (!isset($this->products[$id])) {
      return new JsonResponse(['error' => 'Товар не знайдено'], Response::HTTP_NOT_FOUND);
    }

    return new JsonResponse($this->products[$id], Response::HTTP_OK);
  }


  #[Route('/products', name: 'create_product', methods: ['POST'])]
  public function createProduct(Request $request): JsonResponse
  {
    $data = json_decode($request->getContent(), true);

    $lastId = !empty($this->products) ? max(array_keys($this->products)) : 0;
    $newId = $lastId + 1;

    $newProduct = [
      'id' => $newId,
      'title' => $data['title'] ?? 'Новий товар',
      'price' => $data['price'] ?? 0,
    ];

    $this->products[$newId] = $newProduct;

    return new JsonResponse([
      'message' => 'Товар додано в масив',
      'product' => $newProduct,
      'all_products' => array_values($this->products)
    ], Response::HTTP_CREATED);
  }

  #[Route('/products/{id}', name: 'update_product', methods: ['PUT', 'PATCH'])]
  public function updateProduct(int $id, Request $request): JsonResponse
  {
    if (!isset($this->products[$id])) {
      return new JsonResponse(['error' => 'Товар не знайдено'], Response::HTTP_NOT_FOUND);
    }

    $data = json_decode($request->getContent(), true);


    if (isset($data['title'])) {
      $this->products[$id]['title'] = $data['title'];
    }
    if (isset($data['price'])) {
      $this->products[$id]['price'] = $data['price'];
    }

    return new JsonResponse([
      'message' => "Товар з ID $id оновлено",
      'updated_product' => $this->products[$id]
    ], Response::HTTP_OK);
  }

  #[Route('/products/{id}', name: 'delete_product', methods: ['DELETE'])]
  public function deleteProduct(int $id): JsonResponse
  {
    if (!isset($this->products[$id])) {
      return new JsonResponse(['error' => 'Товар не знайдено'], Response::HTTP_NOT_FOUND);
    }

    unset($this->products[$id]);

    return new JsonResponse([
      'message' => "Товар з ID $id видалено з масиву",
      'remaining_products' => array_values($this->products)
    ], Response::HTTP_OK);
  }
}

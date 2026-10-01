<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
  private EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  #[Route('/api/products', name: 'app_get_products', methods: ['GET'])]
  public function getProducts(): JsonResponse
  {
    $products = $this->entityManager->getRepository(Product::class)->findAll();
    return $this->json($products, Response::HTTP_OK);
  }

  #[Route('/api/products/{id}', name: 'app_get_product_by_id', methods: ['GET'])]
  public function getProductById(string $id): JsonResponse
  {
    /** @var Product|null $product */
    $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

    if (empty($product)) {
      return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
    }

    return $this->json($product, Response::HTTP_OK);
  }

  #[Route('/api/products', name: 'app_create_product', methods: ['POST'])]
  public function createProduct(Request $request): JsonResponse
  {
    $data = json_decode($request->getContent(), true);

    $category = $this->entityManager->getRepository(Category::class)->findOneBy(['id' => $data['category_id'] ?? null]);

    if (empty($category)) {
      return $this->json(['message' => 'Category not found'], Response::HTTP_BAD_REQUEST);
    }

    $product = new Product();
    $product
      ->setName($data['name'] ?? '')
      ->setPrice((string)($data['price'] ?? 0))
      ->setCategory($category);

    $this->entityManager->persist($product);
    $this->entityManager->flush();

    return $this->json($product, Response::HTTP_CREATED);
  }

  #[Route('/api/products/{id}', name: 'app_update_product', methods: ['PATCH', 'PUT'])]
  public function updateProduct(string $id, Request $request): JsonResponse
  {
    /** @var Product|null $product */
    $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

    if (empty($product)) {
      return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
    }

    $data = json_decode($request->getContent(), true);

    if (isset($data['name'])) {
      $product->setName($data['name']);
    }
    if (isset($data['price'])) {
      $product->setPrice((string)$data['price']);
    }
    if (isset($data['category_id'])) {
      $category = $this->entityManager->getRepository(Category::class)->findOneBy(['id' => $data['category_id']]);
      if ($category) {
        $product->setCategory($category);
      }
    }

    $this->entityManager->flush();

    return $this->json($product, Response::HTTP_OK);
  }

  #[Route('/api/products/{id}', name: 'app_delete_product', methods: ['DELETE'])]
  public function deleteProduct(string $id): JsonResponse
  {
    /** @var Product|null $product */
    $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

    if (empty($product)) {
      return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
    }

    $this->entityManager->remove($product);
    $this->entityManager->flush();

    return $this->json(['message' => 'Product deleted successfully'], Response::HTTP_OK);
  }
}

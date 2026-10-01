<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
  private EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  #[Route('/api/categories', name: 'app_get_categories', methods: ['GET'])]
  public function getCategories(): JsonResponse
  {
    $categories = $this->entityManager->getRepository(Category::class)->findAll();
    return $this->json($categories, Response::HTTP_OK);
  }

  #[Route('/api/categories/{id}', name: 'app_get_category_by_id', methods: ['GET'])]
  public function getCategoryById(string $id): JsonResponse
  {
    /** @var Category|null $category */
    $category = $this->entityManager->getRepository(Category::class)->findOneBy(['id' => $id]);

    if (empty($category)) {
      return $this->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
    }

    return $this->json($category, Response::HTTP_OK);
  }

  #[Route('/api/categories', name: 'app_create_category', methods: ['POST'])]
  public function createCategory(Request $request): JsonResponse
  {
    $data = json_decode($request->getContent(), true);

    if (empty($data['name'])) {
      return $this->json(['message' => 'Name is required'], Response::HTTP_BAD_REQUEST);
    }

    $category = new Category();
    $category->setName($data['name']);
    $category->setDescription($data['description'] ?? null);

    $this->entityManager->persist($category);
    $this->entityManager->flush();

    return $this->json($category, Response::HTTP_CREATED);
  }

  #[Route('/api/categories/{id}', name: 'app_update_category', methods: ['PATCH', 'PUT'])]
  public function updateCategory(string $id, Request $request): JsonResponse
  {
    /** @var Category|null $category */
    $category = $this->entityManager->getRepository(Category::class)->findOneBy(['id' => $id]);

    if (empty($category)) {
      return $this->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
    }

    $data = json_decode($request->getContent(), true);

    if (isset($data['name'])) {
      $category->setName($data['name']);
    }
    if (array_key_exists('description', $data)) {
      $category->setDescription($data['description']);
    }

    $this->entityManager->flush();

    return $this->json($category, Response::HTTP_OK);
  }

  #[Route('/api/categories/{id}', name: 'app_delete_category', methods: ['DELETE'])]
  public function deleteCategory(string $id): JsonResponse
  {
    /** @var Category|null $category */
    $category = $this->entityManager->getRepository(Category::class)->findOneBy(['id' => $id]);

    if (empty($category)) {
      return $this->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
    }

    $this->entityManager->remove($category);
    $this->entityManager->flush();

    return $this->json(['message' => 'Category deleted successfully'], Response::HTTP_OK);
  }
}

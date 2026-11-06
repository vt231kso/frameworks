<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CategoryController extends AbstractController
{
  /**
   * @var EntityManagerInterface
   */
  private EntityManagerInterface $entityManager;
  /**
   * @var CategoryRepository
   */
  private CategoryRepository $categoryRepository;

  public function __construct(EntityManagerInterface $entityManager, CategoryRepository $categoryRepository)
  {
    $this->entityManager = $entityManager;
    $this->categoryRepository = $categoryRepository;
  }

  /**
   * @param Request $request
   * @return JsonResponse
   */
  #[Route('/api/categories', name: 'app_get_categories', methods: ['GET'])]
  #[IsGranted('ROLE_CLIENT')]
  public function getCategories(Request $request): JsonResponse
  {
    $allowedParams = ['name', 'description'];

    try {
      $categories = $this->categoryRepository->filterAndPaginate($request, $allowedParams);
    } catch (\InvalidArgumentException $e) {
      return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
    }

    if (empty($categories)) {
      return $this->json(['message' => 'No categories found matching your criteria'], Response::HTTP_NOT_FOUND);
    }

    return $this->json($categories, Response::HTTP_OK);
  }
  /**
   * Отримати  id
   * @param string $id
   * @return JsonResponse
   */
  #[Route('/api/categories/{id}', name: 'app_get_category_by_id', methods: ['GET'])]
  #[IsGranted('ROLE_CLIENT')]
  public function getCategoryById(string $id): JsonResponse
  {
    /** @var Category|null $category */
    $category = $this->entityManager->getRepository(Category::class)->findOneBy(['id' => $id]);

    if (empty($category)) {
      return $this->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
    }

    return $this->json($category, Response::HTTP_OK);
  }

  /**
   * Створити категорію
   * @param Request $request
   * @return JsonResponse
   */
  #[Route('/api/categories', name: 'app_create_category', methods: ['POST'])]
  #[IsGranted('ROLE_MANAGER')]
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

  /**
   * Змінити категорію
   * @param string $id
   * @param Request $request
   * @return JsonResponse
   */
  #[Route('/api/categories/{id}', name: 'app_update_category', methods: ['PATCH', 'PUT'])]
  #[IsGranted('ROLE_MANAGER')]
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

  /**
   * Видалити категорію
   * @param string $id
   * @return JsonResponse
   */
  #[Route('/api/categories/{id}', name: 'app_delete_category', methods: ['DELETE'])]
  #[IsGranted('ROLE_ADMIN')]
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

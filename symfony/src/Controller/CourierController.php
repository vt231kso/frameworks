<?php

namespace App\Controller;

use App\Entity\Courier;
use App\Repository\CourierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CourierController extends AbstractController
{
  /**
   * @var EntityManagerInterface
   */
  private EntityManagerInterface $entityManager;

  /**
   * @var CourierRepository
   */
  private CourierRepository $courierRepository;

  public function __construct(EntityManagerInterface $entityManager, CourierRepository $courierRepository )
  {
    $this->entityManager = $entityManager;
    $this->courierRepository = $courierRepository;
  }

  /**
   * @param Request $request
   * @return JsonResponse
   */
  #[Route('/api/couriers', name: 'app_get_couriers', methods: ['GET'])]
  public function getCouriers(Request $request): JsonResponse
  {
    $allowedParams = ['name', 'phone', 'vehicle_type'];

    try {
      $couriers = $this->courierRepository->filterAndPaginate($request, $allowedParams);
    } catch (\InvalidArgumentException $e) {
      return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
    }

    if (empty($couriers)) {
      return $this->json(['message' => 'No couriers found matching your criteria'], Response::HTTP_NOT_FOUND);
    }

    return $this->json($couriers, Response::HTTP_OK);
  }
  /**
   * Отримати за id
   * @param string $id
   * @return JsonResponse
   */
  #[Route('/api/couriers/{id}', name: 'app_get_courier_by_id', methods: ['GET'])]
  public function getCourierById(string $id): JsonResponse
  {
    /** @var Courier|null $courier */
    $courier = $this->entityManager->getRepository(Courier::class)->findOneBy(['id' => $id]);

    if (empty($courier)) {
      return $this->json(['message' => 'Courier not found'], Response::HTTP_NOT_FOUND);
    }

    return $this->json($courier, Response::HTTP_OK);
  }

  /**
   * Створити курєра
   * @param Request $request
   * @return JsonResponse
   */
  #[Route('/api/couriers', name: 'app_create_courier', methods: ['POST'])]
  public function createCourier(Request $request): JsonResponse
  {
    $data = json_decode($request->getContent(), true);

    $courier = new Courier();
    $courier
      ->setName($data['name'] ?? '')
      ->setPhone($data['phone'] ?? '')
      ->setVehicleType($data['vehicle_type'] ?? null);

    $this->entityManager->persist($courier);
    $this->entityManager->flush();

    return $this->json($courier, Response::HTTP_CREATED);
  }

  /**
   * Змінити курєра
   * @param string $id
   * @param Request $request
   * @return JsonResponse
   */
  #[Route('/api/couriers/{id}', name: 'app_update_courier', methods: ['PATCH', 'PUT'])]
  public function updateCourier(string $id, Request $request): JsonResponse
  {
    /** @var Courier|null $courier */
    $courier = $this->entityManager->getRepository(Courier::class)->findOneBy(['id' => $id]);

    if (empty($courier)) {
      return $this->json(['message' => 'Courier not found'], Response::HTTP_NOT_FOUND);
    }

    $data = json_decode($request->getContent(), true);

    if (isset($data['name'])) {
      $courier->setName($data['name']);
    }
    if (isset($data['phone'])) {
      $courier->setPhone($data['phone']);
    }
    if (array_key_exists('vehicle_type', $data)) {
      $courier->setVehicleType($data['vehicle_type']);
    }

    $this->entityManager->flush();

    return $this->json($courier, Response::HTTP_OK);
  }

  /**
   * Видалити курєра
   * @param string $id
   * @return JsonResponse
   */
  #[Route('/api/couriers/{id}', name: 'app_delete_courier', methods: ['DELETE'])]
  public function deleteCourier(string $id): JsonResponse
  {
    /** @var Courier|null $courier */
    $courier = $this->entityManager->getRepository(Courier::class)->findOneBy(['id' => $id]);

    if (empty($courier)) {
      return $this->json(['message' => 'Courier not found'], Response::HTTP_NOT_FOUND);
    }

    $this->entityManager->remove($courier);
    $this->entityManager->flush();

    return $this->json(['message' => 'Courier deleted successfully'], Response::HTTP_OK);
  }
}

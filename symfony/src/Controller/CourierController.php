<?php

namespace App\Controller;

use App\Entity\Courier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CourierController extends AbstractController
{
  private EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  #[Route('/api/couriers', name: 'app_get_couriers', methods: ['GET'])]
  public function getCouriers(): JsonResponse
  {
    $couriers = $this->entityManager->getRepository(Courier::class)->findAll();
    return $this->json($couriers, Response::HTTP_OK);
  }

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

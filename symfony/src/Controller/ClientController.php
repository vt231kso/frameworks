<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ClientController extends AbstractController
{
  /**
   * @var EntityManagerInterface
   */
  private EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  /**
   * Отримати клієнтів
   * @return JsonResponse
   */
  #[Route('/api/clients', name: 'app_get_clients', methods: ['GET'])]
  public function getClients(): JsonResponse
  {
    $clients = $this->entityManager->getRepository(Client::class)->findAll();
    return $this->json($clients, Response::HTTP_OK);
  }

  /**
   * Отримати за id
   * @param string $id
   * @return JsonResponse
   */
  #[Route('/api/clients/{id}', name: 'app_get_client_by_id', methods: ['GET'])]
  public function getClientById(string $id): JsonResponse
  {
    /** @var Client|null $client */
    $client = $this->entityManager->getRepository(Client::class)->findOneBy(['id' => $id]);

    if (empty($client)) {
      return $this->json(['message' => 'Client not found'], Response::HTTP_NOT_FOUND);
    }

    return $this->json($client, Response::HTTP_OK);
  }

  /**
   * Створити клієнта
   * @param Request $request
   * @return JsonResponse
   */
  #[Route('/api/clients', name: 'app_create_client', methods: ['POST'])]
  public function createClient(Request $request): JsonResponse
  {
    $data = json_decode($request->getContent(), true);

    $client = new Client();
    $client
      ->setName($data['name'] ?? '')
      ->setPhone($data['phone'] ?? '')
      ->setEmail($data['email'] ?? '');

    $this->entityManager->persist($client);
    $this->entityManager->flush();

    return $this->json($client, Response::HTTP_CREATED);
  }

  /**
   * Змінити клієнта
   * @param string $id
   * @param Request $request
   * @return JsonResponse
   */
  #[Route('/api/clients/{id}', name: 'app_update_client', methods: ['PATCH', 'PUT'])]
  public function updateClient(string $id, Request $request): JsonResponse
  {
    /** @var Client|null $client */
    $client = $this->entityManager->getRepository(Client::class)->findOneBy(['id' => $id]);

    if (empty($client)) {
      return $this->json(['message' => 'Client not found'], Response::HTTP_NOT_FOUND);
    }

    $data = json_decode($request->getContent(), true);

    if (isset($data['name'])) {
      $client->setName($data['name']);
    }
    if (isset($data['phone'])) {
      $client->setPhone($data['phone']);
    }
    if (isset($data['email'])) {
      $client->setEmail($data['email']);
    }

    $this->entityManager->flush();

    return $this->json($client, Response::HTTP_OK);
  }

  /**
   * Видалити клієнта
   * @param string $id
   * @return JsonResponse
   */
  #[Route('/api/clients/{id}', name: 'app_delete_client', methods: ['DELETE'])]
  public function deleteClient(string $id): JsonResponse
  {
    /** @var Client|null $client */
    $client = $this->entityManager->getRepository(Client::class)->findOneBy(['id' => $id]);

    if (empty($client)) {
      return $this->json(['message' => 'Client not found'], Response::HTTP_NOT_FOUND);
    }

    $this->entityManager->remove($client);
    $this->entityManager->flush();

    return $this->json(['message' => 'Client deleted successfully'], Response::HTTP_OK);
  }
}

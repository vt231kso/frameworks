<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ClientController extends AbstractController
{
  /**
   * @var EntityManagerInterface
   */
  private EntityManagerInterface $entityManager;
  /**
   * @var ClientRepository
   */
  private ClientRepository $clientRepository;

  public function __construct(EntityManagerInterface $entityManager, ClientRepository $clientRepository)
  {
    $this->entityManager = $entityManager;
    $this->clientRepository = $clientRepository;
  }
  /**
   * Отримати профіль поточного авторизованого клієнта
   */
  #[Route('/api/clients/me', name: 'app_get_current_client', methods: ['GET'])]
  #[IsGranted('ROLE_CLIENT')]
  public function getCurrentClient(): JsonResponse
  {
    /** @var UserInterface $currentUser */
    $currentUser = $this->getUser();
    $userEmail = method_exists($currentUser, 'getEmail')
      ? $currentUser->getEmail()
      : $currentUser->getUserIdentifier();

    /** @var Client|null $client */
    $client = $this->clientRepository->findOneBy(['email' => $userEmail]);

    if (empty($client)) {
      return $this->json(['message' => 'Client profile not found for current user'], Response::HTTP_NOT_FOUND);
    }

    return $this->json($client, Response::HTTP_OK);
  }


  /**
   * @param Request $request
   * @return JsonResponse
   */
  #[Route('/api/clients', name: 'app_get_clients', methods: ['GET'])]
  #[IsGranted('ROLE_MANAGER')]
  public function getClients(Request $request): JsonResponse
  {
    $allowedParams = ['name', 'phone', 'email'];

    try {
      $clients = $this->clientRepository->filterAndPaginate($request, $allowedParams);
    } catch (\InvalidArgumentException $e) {
      return $this->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
    }

    if (empty($clients)) {
      return $this->json(['message' => 'No clients found matching your criteria'], Response::HTTP_NOT_FOUND);
    }

    return $this->json($clients, Response::HTTP_OK);
  }
  /**
   * Отримати за id
   * @param string $id
   * @return JsonResponse
   */
  #[Route('/api/clients/{id}', name: 'app_get_client_by_id', methods: ['GET'])]
  #[IsGranted('ROLE_MANAGER')]
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
  #[IsGranted('ROLE_MANAGER')]
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
  #[IsGranted('ROLE_MANAGER')]
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
  #[IsGranted('ROLE_ADMIN')]
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

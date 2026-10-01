<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Courier;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
  private EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  #[Route('/api/orders', name: 'app_get_orders', methods: ['GET'])]
  public function getOrders(): JsonResponse
  {
    $orders = $this->entityManager->getRepository(Order::class)->findAll();
    return $this->json($orders, Response::HTTP_OK);
  }

  #[Route('/api/orders/{id}', name: 'app_get_order_by_id', methods: ['GET'])]
  public function getOrderById(string $id): JsonResponse
  {
    /** @var Order|null $order */
    $order = $this->entityManager->getRepository(Order::class)->findOneBy(['id' => $id]);

    if (empty($order)) {
      return $this->json(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
    }

    return $this->json($order, Response::HTTP_OK);
  }

  #[Route('/api/orders', name: 'app_create_order', methods: ['POST'])]
  public function createOrder(Request $request): JsonResponse
  {
    $data = json_decode($request->getContent(), true);

    $client = $this->entityManager->getRepository(Client::class)->findOneBy(['id' => $data['client_id'] ?? null]);
    $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $data['product_id'] ?? null]);
    $courier = isset($data['courier_id'])
      ? $this->entityManager->getRepository(Courier::class)->findOneBy(['id' => $data['courier_id']])
      : null;

    if (empty($client) || empty($product)) {
      return $this->json(['message' => 'Client or Product not found'], Response::HTTP_BAD_REQUEST);
    }

    $order = new Order();
    $order
      ->setClient($client)
      ->setProduct($product)
      ->setCourier($courier)
      ->setDeliveryAddress($data['delivery_address'] ?? '')
      ->setTotalAmount((string)($data['total_amount'] ?? 0))
      ->setStatus($data['status'] ?? 'pending');

    $this->entityManager->persist($order);
    $this->entityManager->flush();

    return $this->json($order, Response::HTTP_CREATED);
  }

  #[Route('/api/orders/{id}', name: 'app_update_order', methods: ['PATCH', 'PUT'])]
  public function updateOrder(string $id, Request $request): JsonResponse
  {
    /** @var Order|null $order */
    $order = $this->entityManager->getRepository(Order::class)->findOneBy(['id' => $id]);

    if (empty($order)) {
      return $this->json(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
    }

    $data = json_decode($request->getContent(), true);

    if (isset($data['client_id'])) {
      $client = $this->entityManager->getRepository(Client::class)->findOneBy(['id' => $data['client_id']]);
      if ($client) {
        $order->setClient($client);
      }
    }
    if (isset($data['product_id'])) {
      $product = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $data['product_id']]);
      if ($product) {
        $order->setProduct($product);
      }
    }
    if (array_key_exists('courier_id', $data)) {
      $courier = $data['courier_id'] ? $this->entityManager->getRepository(Courier::class)->findOneBy(['id' => $data['courier_id']]) : null;
      $order->setCourier($courier);
    }
    if (isset($data['delivery_address'])) {
      $order->setDeliveryAddress($data['delivery_address']);
    }
    if (isset($data['total_amount'])) {
      $order->setTotalAmount((string)$data['total_amount']);
    }
    if (isset($data['status'])) {
      $order->setStatus($data['status']);
    }

    $this->entityManager->flush();

    return $this->json($order, Response::HTTP_OK);
  }

  #[Route('/api/orders/{id}', name: 'app_delete_order', methods: ['DELETE'])]
  public function deleteOrder(string $id): JsonResponse
  {
    /** @var Order|null $order */
    $order = $this->entityManager->getRepository(Order::class)->findOneBy(['id' => $id]);

    if (empty($order)) {
      return $this->json(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
    }

    $this->entityManager->remove($order);
    $this->entityManager->flush();

    return $this->json(['message' => 'Order deleted successfully'], Response::HTTP_OK);
  }
}

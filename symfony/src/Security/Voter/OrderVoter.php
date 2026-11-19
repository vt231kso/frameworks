<?php

namespace App\Security\Voter;

use App\Entity\Client;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class OrderVoter extends Voter
{
  public const VIEW = 'ORDER_VIEW';

  public function __construct(
    private readonly Security $security,
    private readonly EntityManagerInterface $entityManager,
  ) {
  }

  /**
   * Визначає, чи відповідає цей Voter за поточну перевірку
   */
  protected function supports(string $attribute, mixed $subject): bool
  {
    return $attribute === self::VIEW && $subject instanceof Order;
  }

  /**
   * Сама логіка перевірки (аналог before + view у Laravel OrderPolicy)
   */
  protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
  {
    $user = $token->getUser();

    if (!$user instanceof User) {
      return false;
    }

    // Менеджер та Адмін (через role_hierarchy) бачать будь-яке замовлення
    if ($this->security->isGranted('ROLE_MANAGER')) {
      return true;
    }

    /** @var Order $order */
    $order = $subject;
    $orderClient = $order->getClient();

    if (!$orderClient) {
      return false;
    }

    // Шукаємо профіль клієнта за email поточного користувача
    $currentClientProfile = $this->entityManager
      ->getRepository(Client::class)
      ->findOneBy(['email' => $user->getUserIdentifier()]);

    return $currentClientProfile !== null
      && $orderClient->getId() === $currentClientProfile->getId();
  }
}

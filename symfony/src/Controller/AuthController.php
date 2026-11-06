<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{
  #[Route('/api/register', name: 'api_register', methods: ['POST'])]
  public function register(
    Request $request,
    UserPasswordHasherInterface $hasher,
    ValidatorInterface $validator,
    EntityManagerInterface $em
  ): JsonResponse {
    $data = $request->toArray();
    $plain = (string) ($data['password'] ?? '');

    if (mb_strlen($plain) < 8) {
      return $this->json(['error' => 'Пароль має містити щонайменше 8 символів'], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $user = (new User())->setEmail((string) ($data['email'] ?? ''));
    $user->setPassword($hasher->hashPassword($user, $plain));

    if (!empty($data['roles']) && is_array($data['roles'])) {
      $user->setRoles($data['roles']);
    }

    $errors = $validator->validate($user);
    if (count($errors) > 0) {
      return $this->json(['error' => (string) $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $em->persist($user);
    $em->flush();

    return $this->json([
      'id' => $user->getId(),
      'email' => $user->getEmail(),
      'roles' => $user->getRoles(),
    ], Response::HTTP_CREATED);
  }

  #[Route('/api/me', name: 'api_me', methods: ['GET'])]
  public function me(): JsonResponse
  {
    /** @var User $user */
    $user = $this->getUser();

    return $this->json([
      'email' => $user->getEmail(),
      'roles' => $user->getRoles(),
    ]);
  }
}

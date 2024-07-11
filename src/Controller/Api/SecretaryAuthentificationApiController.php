<?php

// src/Controller/Api/SecretaryAuthentificationApiController.php

namespace App\Controller\Api;

use App\Entity\Secretary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecretaryAuthentificationApiController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;
    private $tokenGenerator;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, TokenGeneratorInterface $tokenGenerator)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->tokenGenerator = $tokenGenerator;
    }

    #[Route('/api/secretary/login', name: 'api_secretary_login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $rawContent = $request->getContent();
        $data = json_decode($rawContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['message' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['message' => 'Email and password are required'], Response::HTTP_BAD_REQUEST);
        }

        $email = $data['email'];
        $password = $data['password'];

        $secretary = $this->entityManager->getRepository(Secretary::class)->findOneBy(['email' => $email]);

        if (!$secretary) {
            return new JsonResponse(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->passwordHasher->isPasswordValid($secretary, $password)) {
            return new JsonResponse(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $csrfToken = $this->tokenGenerator->generateToken();

        return new JsonResponse([
            'message' => 'Success',
            'csrf_token' => $csrfToken
        ], Response::HTTP_OK);
    }
}

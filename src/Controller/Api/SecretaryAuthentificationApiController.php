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

class SecretaryAuthentificationApiController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/api/secretary/login', name: 'api_secretary_login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        // Log the raw content of the request
        $rawContent = $request->getContent();
        file_put_contents('php://stderr', print_r($rawContent, true));

        $data = json_decode($rawContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Log JSON decode error
            file_put_contents('php://stderr', print_r(json_last_error_msg(), true));
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

        return new JsonResponse(['message' => 'Success'], Response::HTTP_OK);
    }
}

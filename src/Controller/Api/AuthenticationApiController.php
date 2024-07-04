<?php

namespace App\Controller\Api;

use App\DTO\LoginDTO;
use App\Repository\DoctorsRepository;
use App\Repository\UsersRepository;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/api/auth")]
class AuthenticationApiController extends AbstractController
{
    #[Route("/login", methods: ["POST"])]
    public function login(
        #[MapRequestPayload] LoginDTO $loginDTO,
        DoctorsRepository $doctorsRepository
    ) {
        $doctor = $doctorsRepository->findOneBy(["lastname" => $loginDTO->lastname]);

        if ($doctor) {
            if ($doctor->getIdentification() === $loginDTO->identification) {
                $key = 'example_key';
                $payload = [
                    'iat' => time(),
                    'exp' => time() + 3600 * 24,
                    'userId' => $doctor->getId()
                ];
                $jwt = JWT::encode($payload, $key, "HS256");
                return $this->json(["token" => $jwt]);
            }
            return $this->json(["error" => "Bad credentials"], Response::HTTP_NOT_FOUND);
        }
        return $this->json(["error" => "Bad credentials"], Response::HTTP_NOT_FOUND);
    }
}

<?php

namespace App\Controller\Api;

use App\Context\SecretaryApiContext;
use App\Entity\Secretary;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class SecretaryAuthentificationApiController extends AbstractController
{
    private SecretaryApiContext $secretaryApiContext;
    private TokenStorageInterface $tokenStorage;
    private LoggerInterface $logger;
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(
        SecretaryApiContext $secretaryApiContext,
        TokenStorageInterface $tokenStorage,
        LoggerInterface $logger,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->secretaryApiContext = $secretaryApiContext;
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[Route('/api/auth/login_secretary', name: 'login_secretary', methods: ['POST'])]
    public function login_secretary(Request $request, JWTTokenManagerInterface $jwtTokenManager): Response
    {
        $this->logger->info('Raw request content', ['content' => $request->getContent()]);

        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $this->logger->info('Received credentials', [
            'email' => $email,
            'password' => $password,
        ]);

        if (!$email || !$password) {
            throw new UnauthorizedHttpException('Authentication required');
        }

        $secretary = $this->tokenStorage->getToken()->getUser();
        if ($secretary instanceof Secretary) {
            $this->secretaryApiContext->setSecretary($secretary);

            $csrfToken = $this->csrfTokenManager->getToken('authenticate');
            $csrfTokenValue = $csrfToken->getValue();

            $token = $jwtTokenManager->create($secretary);
            return new JsonResponse(['status' => 'success', 'csrf_token' => $csrfTokenValue, 'token' => $token]);
        }

        throw new UnauthorizedHttpException('Authentication required');
    }
}

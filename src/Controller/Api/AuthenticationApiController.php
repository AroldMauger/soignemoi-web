<?php
namespace App\Controller\Api;

use App\Context\DoctorApiContext;
use App\Entity\Doctors;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class AuthenticationApiController extends AbstractController
{
    private DoctorApiContext $doctorApiContext;
    private TokenStorageInterface $tokenStorage;
    private LoggerInterface $logger;
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function getFirewallName(): string
    {
        return 'doctor';
    }

    public function __construct(DoctorApiContext $doctorApiContext, TokenStorageInterface $tokenStorage, LoggerInterface $logger, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->doctorApiContext = $doctorApiContext;
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    #[Route('/api/auth/login_doctor', name: 'login_doctor', methods: ['POST'])]
    public function login_doctor(Request $request, JWTTokenManagerInterface $jwtTokenManager): Response
    {
        // Ajoutez ce log pour voir le contenu brut de la requête
        $this->logger->info('Raw request content', ['content' => $request->getContent()]);

        $lastname = $request->request->get('lastname');
        $identification = $request->request->get('identification');

        // Ajoutez ce log pour voir les valeurs extraites
        $this->logger->info('Received credentials', [
            'lastname' => $lastname,
            'identification' => $identification,
        ]);

        if (!$lastname || !$identification) {
            throw new UnauthorizedHttpException('Authentication required');
        }

        $doctor = $this->tokenStorage->getToken()->getUser();
        if ($doctor instanceof Doctors) {
            $this->doctorApiContext->setDoctors($doctor);

            $csrfToken = $this->csrfTokenManager->getToken('authenticate');
            $csrfTokenValue = $csrfToken->getValue();

            // Generate a JWT token
            $token = $jwtTokenManager->create($doctor, ['roles' => $doctor->getRoles()]);
            // Return the CSRF token and JWT token in the response
            return new JsonResponse(['status' => 'success', 'csrf_token' => $csrfTokenValue, 'token' => $token,]);
        }

        throw new UnauthorizedHttpException('Authentication required');
    }
}
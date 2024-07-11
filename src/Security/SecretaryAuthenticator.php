<?php

namespace App\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

class SecretaryAuthenticator extends AbstractLoginFormAuthenticator
{
    private \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher;
    private LoggerInterface $logger;
    private UserProviderInterface $userProvider;
    private UrlGeneratorInterface $urlGenerator;
    private CsrfTokenManagerInterface $csrfTokenManager;
    public function getFirewallName(): string
    {
        return 'secretary';
    }
    public function __construct(
        \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher,
        LoggerInterface $logger,
        UserProviderInterface $userProvider,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->logger = $logger;
        $this->userProvider = $userProvider;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        $csrfToken = $this->csrfTokenManager->getToken('authenticate');
        $csrfTokenValue = $csrfToken->getValue();

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $csrfTokenValue),
                new RememberMeBadge(),
            ]
        );
    }

    public function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('login_secretary');
    }
    protected function getUserProvider(Request $request): UserProviderInterface
    {
        return $this->userProvider;
    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($firewallName === 'secretary') {
            $secretary= $token->getUser();
            $secretaryEmail = $secretary->getEmail(); // Récupération de l'email

            // Stockage du nom du docteur dans une variable globale ou session
            $request->getSession()->set('email', $secretaryEmail);

            $this->logger->info('Secretary authentication success for user ' . $token->getUserIdentifier());
        } else {
            $this->logger->info('Authentication success for user ' . $token->getUserIdentifier());
        }

        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $this->logger->error('Authentication failed: ' . $exception->getMessage());
        return new Response('Authentication Failed', Response::HTTP_UNAUTHORIZED);
    }
}
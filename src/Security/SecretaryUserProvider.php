<?php

namespace App\Security;

use App\Entity\Secretary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SecretaryUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->entityManager
            ->getRepository(Secretary::class)
            ->findOneBy(['email' => $identifier])
            ?? throw new \Symfony\Component\Security\Core\Exception\UserNotFoundException('Secretary not found.');
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof Secretary) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByIdentifier($user->getEmail());
    }

    public function supportsClass(string $class): bool
    {
        return Secretary::class === $class;
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Secretary) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}

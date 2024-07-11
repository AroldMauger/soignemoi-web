<?php

namespace App\Command;

use App\Entity\Secretary;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:hash-secretary-password', description: 'Hashes the password for a secretary.')]
class HashSecretaryPasswordCommand extends Command
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the secretary')
            ->addArgument('password', InputArgument::REQUIRED, 'The password to hash');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $secretary = $this->entityManager->getRepository(Secretary::class)->findOneBy(['email' => $email]);

        if (!$secretary) {
            $output->writeln('Secretary not found');
            return Command::FAILURE;
        }

        $hashedPassword = $this->passwordHasher->hashPassword($secretary, $password);
        $secretary->setPassword($hashedPassword);
        $this->entityManager->flush();

        $output->writeln('Password hashed successfully');

        return Command::SUCCESS;
    }
}

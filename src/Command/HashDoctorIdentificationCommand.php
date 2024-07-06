<?php
namespace App\Command;

use App\Entity\Doctors;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:hash-doctor-identification')]
class HashDoctorIdentificationCommand extends Command
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->addArgument('lastname', InputArgument::REQUIRED, 'The lastname of the doctor')
            ->addArgument('identification', InputArgument::REQUIRED, 'The identification to hash');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $lastname = $input->getArgument('lastname');
        $identification = $input->getArgument('identification');

        $doctor = $this->entityManager->getRepository(Doctors::class)->findOneBy(['lastname' => $lastname]);

        if (!$doctor) {
            $output->writeln('Doctor not found');
            return Command::FAILURE;
        }

        $hashedIdentification = $this->passwordHasher->hashPassword($doctor, $identification);
        $doctor->setPassword($hashedIdentification);

        $this->entityManager->flush();

        $output->writeln('Identification hashed successfully');

        return Command::SUCCESS;
    }
}
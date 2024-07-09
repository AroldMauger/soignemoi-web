<?php

namespace App\Repository;

use App\Entity\Opinions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OpinionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Opinions::class);
    }

    public function save(Opinions $opinion): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($opinion);
        $entityManager->flush();
    }
}

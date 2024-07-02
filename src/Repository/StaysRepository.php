<?php
// src/Repository/StaysRepository.php
// src/Repository/StaysRepository.php

namespace App\Repository;

use App\Entity\Stays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

class StaysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stays::class);
    }

    // Méthode pour récupérer les séjours en cours
    public function findCurrentStays(): array
    {
        $now = new DateTime();
        return $this->createQueryBuilder('s')
            ->leftJoin('s.doctor', 'd')
            ->addSelect('d')
            ->where('s.entrydate <= :now')
            ->andWhere('s.leavingdate >= :now')
            ->setParameter('now', $now)
            ->orderBy('s.entrydate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // Méthode pour récupérer les séjours à venir
    public function findUpcomingStays(): array
    {
        $now = new DateTime();
        return $this->createQueryBuilder('s')
            ->leftJoin('s.doctor', 'd')
            ->addSelect('d')
            ->where('s.entrydate > :now')
            ->setParameter('now', $now)
            ->orderBy('s.entrydate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}


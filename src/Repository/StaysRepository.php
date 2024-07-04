<?php
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

    // Méthode pour mettre à jour le statut des séjours
    public function updateStayStatuses(): void
    {
        $now = new DateTime();
        $this->createQueryBuilder('s')
            ->update()
            ->set('s.status', ':status')
            ->where('s.entrydate <= :now')
            ->andWhere('s.leavingdate >= :now')
            ->setParameter('now', $now)
            ->setParameter('status', 'en cours')
            ->getQuery()
            ->execute();

        $this->createQueryBuilder('s')
            ->update()
            ->set('s.status', ':status')
            ->where('s.entrydate > :now')
            ->setParameter('now', $now)
            ->setParameter('status', 'à venir')
            ->getQuery()
            ->execute();

        $this->createQueryBuilder('s')
            ->update()
            ->set('s.status', ':status')
            ->where('s.leavingdate < :now')
            ->setParameter('now', $now)
            ->setParameter('status', 'terminé')
            ->getQuery()
            ->execute();
    }

    public function findFinishedPaginated (int $page, int $limit) {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.status = :status')
            ->setParameter('status', "terminé")
            ->setFirstResult($page * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    public function countAllInProgress()
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where('a.status = :status')
            ->setParameter('status', 'en cours')
            ->getQuery()
            ->getSingleScalarResult();
    }

}


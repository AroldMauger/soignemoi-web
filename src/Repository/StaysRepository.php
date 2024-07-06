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
            ->leftJoin('s.slot', 'sl') // Jointure avec l'entité Slot
            ->addSelect('sl')
            ->where('s.entrydate <= :now')
            ->andWhere('s.leavingdate >= :now')
            ->andWhere('sl.isbooked = true') // Condition pour vérifier si le slot est booké
            ->setParameter('now', $now)
            ->orderBy('s.entrydate', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findByDoctorLastName(string $lastname): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.doctor', 'd') // Jointure avec l'entité Doctor
            ->addSelect('d')
            ->where('d.lastname = :lastname')
            ->setParameter('lastname', $lastname)
            ->getQuery()
            ->getResult();
    }

    // Méthode pour récupérer les séjours à venir
    public function findUpcomingStays(): array
    {
        $now = new DateTime();
        return $this->createQueryBuilder('s')
            ->leftJoin('s.slot', 'sl') // Jointure avec l'entité Slot
            ->addSelect('sl')
            ->where('s.entrydate > :now')
            ->andWhere('sl.isbooked = true') // Condition pour vérifier si le slot est booké
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

    public function findFinishedPaginated(int $page, int $limit): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.slot', 'sl') // Jointure avec l'entité Slot
            ->addSelect('sl')
            ->where('a.status = :status')
            ->andWhere('sl.isbooked = true') // Condition pour vérifier si le slot est booké
            ->setParameter('status', 'terminé')
            ->setFirstResult($page * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countAllInProgress(): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->leftJoin('a.slot', 'sl') // Jointure avec l'entité Slot
            ->addSelect('sl')
            ->where('a.status = :status')
            ->andWhere('sl.isbooked = true') // Condition pour vérifier si le slot est booké
            ->setParameter('status', 'en cours')
            ->getQuery()
            ->getSingleScalarResult();
    }
}

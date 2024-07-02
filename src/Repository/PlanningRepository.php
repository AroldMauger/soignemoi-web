<?php

// src/Repository/PlanningRepository.php
// src/Repository/PlanningRepository.php

namespace App\Repository;

use App\Entity\Planning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PlanningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Planning::class);
    }

    public function findAvailableSlots($doctorId, \DateTime $date)
    {
        return $this->createQueryBuilder('p')
            ->join('p.slots', 's')
            ->where('p.doctor = :doctorId')
            ->andWhere('p.date = :date')
            ->andWhere('s.isBooked = false')  // Assurez-vous que 'isBooked' est la bonne propriété
            ->setParameter('doctorId', $doctorId)
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
}

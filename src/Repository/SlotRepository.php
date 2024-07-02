<?php

namespace App\Repository;

use App\Entity\Slot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slot::class);
    }

    public function findAvailableSlots(int $doctorId, \DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.planning', 'p')  // Joindre Planning pour obtenir des slots spécifiques
            ->where('s.isbooked = :isBooked')
            ->andWhere('p.doctor = :doctorId')
            ->andWhere('p.date = :date')  // Filtrer les slots pour la date du planning
            ->setParameter('isBooked', false)
            ->setParameter('doctorId', $doctorId)
            ->setParameter('date', $date->format('Y-m-d'))  // Assurez-vous d'envoyer uniquement la date
            ->getQuery()
            ->getResult();
    }

}

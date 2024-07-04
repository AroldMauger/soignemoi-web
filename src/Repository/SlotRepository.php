<?php
namespace App\Repository;

use App\Entity\Doctors;  // Importer l'entité correcte
use App\Entity\Slot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slot::class);
    }

    // src/Repository/SlotRepository.php

    // src/Repository/SlotRepository.php

    public function findAvailableSlots(Doctors $doctor, \DateTime $dateStart, ?\DateTime $dateEnd = null): array
    {
        // Exemple de requête pour trouver des créneaux disponibles
        $queryBuilder = $this->createQueryBuilder('s')
            ->where('s.doctor = :doctor')
            ->andWhere('s.starttime >= :starttime')
            ->setParameter('doctor', $doctor)
            ->setParameter('starttime', $dateStart)
            ->orderBy('s.starttime', 'ASC');

        if ($dateEnd) {
            $queryBuilder->andWhere('s.endtime <= :endtime')
                ->setParameter('endtime', $dateEnd);
        }

        return $queryBuilder->getQuery()->getResult();
    }


}


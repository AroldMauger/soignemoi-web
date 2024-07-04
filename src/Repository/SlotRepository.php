<?php
namespace App\Repository;

use App\Entity\Doctors;
use App\Entity\Slot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slot::class);
    }

    /**
     * Trouver les créneaux disponibles pour un médecin et une période donnée.
     *
     * @param \DateTime $dateStart
     * @param \DateTime|null $dateEnd
     * @return Slot[]
     */

    public function findAvailableSlots(Doctors $doctor, \DateTime $dateStart, ?\DateTime $dateEnd = null): array
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->innerJoin('s.planning', 'p')  // Joindre Planning pour accéder à la date
            ->where('s.doctor = :doctor')
            ->andWhere('p.date = :date')  // Filtrer par date du planning
            ->andWhere('s.isbooked = false')
            ->setParameter('doctor', $doctor)
            ->setParameter('date', $dateStart->format('Y-m-d'))  // Assurez-vous que la date est au format correct
            ->orderBy('s.starttime', 'ASC');

        // Ajoutez la condition pour `dateEnd` seulement si elle est définie
        if ($dateEnd) {
            $queryBuilder->andWhere('s.endtime <= :endtime')
                ->setParameter('endtime', $dateEnd->format('H:i:s'));
        }

        return $queryBuilder->getQuery()->getResult();
    }


}


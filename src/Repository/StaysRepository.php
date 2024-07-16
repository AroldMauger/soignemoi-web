<?php
// src/Repository/StaysRepository.php

namespace App\Repository;

use App\Entity\Stays;
use App\Entity\Users;
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
    public function findCurrentStays(Users $user): array
    {
        $now = new DateTime();  // Utilisation de DateTime() pour obtenir l'heure actuelle
        return $this->createQueryBuilder('s')
            ->leftJoin('s.slot', 'sl')  // Jointure avec l'entité Slot
            ->addSelect('sl')
            ->where('s.entrydate <= :now')  // Date d'entrée doit être avant ou égale à maintenant
            ->andWhere('s.leavingdate >= :now')  // Date de départ doit être après ou égale à maintenant
            ->andWhere('sl.isbooked = true')  // Le slot doit être réservé
            ->andWhere('s.user = :user')  // Le séjour doit appartenir à l'utilisateur
            ->andWhere('s.status = :statusEnCours')  // Le statut du séjour doit être "en cours"
            ->setParameter('now', $now)
            ->setParameter('user', $user)
            ->setParameter('statusEnCours', 'en cours')  // Définir le paramètre du statut à "en cours"
            ->orderBy('s.entrydate', 'ASC')  // Tri des séjours par date d'entrée
            ->getQuery()
            ->getResult();
    }





    public function findByDoctorLastName(string $lastName): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.doctor', 'd')
            ->andWhere('d.lastname = :lastname')
            ->setParameter('lastname', $lastName)
            ->getQuery()
            ->getResult();
    }


    public function findByDoctorId($doctorId)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.doctor = :doctorId')
            ->setParameter('doctorId', $doctorId)
            ->getQuery()
            ->getResult();
    }

    // Méthode pour récupérer les séjours à venir
    public function findUpcomingStays(Users $user): array
    {
        $now = new DateTime();
        return $this->createQueryBuilder('s')
            ->leftJoin('s.slot', 'sl') // Jointure avec l'entité Slot
            ->addSelect('sl')
            ->where('s.entrydate > :now')
            ->andWhere('sl.isbooked = true') // Condition pour vérifier si le slot est booké
            ->andWhere('s.user = :user')
            ->andWhere('s.status = :statusAvenir') // Condition pour inclure les séjours "à venir"
            ->setParameter('now', $now)
            ->setParameter('user', $user)
            ->setParameter('statusAvenir', 'à venir') // Assurez-vous que le statut correspond à "à venir"
            ->orderBy('s.entrydate', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findNonTerminatedStays(): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.slot', 'sl') // Jointure avec l'entité Slot
            ->addSelect('sl')
            ->where('s.status != :statusTermine')
            ->andWhere('sl.isbooked = true') // Condition pour vérifier si le slot est booké
            ->setParameter('statusTermine', 'terminé')
            ->getQuery()
            ->getResult();
    }
    public function findNonTerminatedStaysByDoctorLastName(string $lastName): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.doctor', 'd')
            ->leftJoin('s.slot', 'sl') // Jointure avec l'entité Slot
            ->addSelect('d', 'sl')
            ->where('d.lastname = :lastname')
            ->andWhere('s.status != :statusTermine')
            ->andWhere('sl.isbooked = true') // Condition pour vérifier si le slot est booké
            ->setParameter('lastname', $lastName)
            ->setParameter('statusTermine', 'terminé')
            ->getQuery()
            ->getResult();
    }
    // Méthode pour mettre à jour le statut des séjours
    public function updateStayStatuses(): void
    {
        $now = new DateTime();

        // Met à jour le statut des séjours en cours
        $this->createQueryBuilder('s')
            ->update()
            ->set('s.status', ':status')
            ->where('s.entrydate <= :now')
            ->andWhere('s.leavingdate >= :now')
            ->setParameter('now', $now)
            ->setParameter('status', 'en cours')
            ->getQuery()
            ->execute();

        // Met à jour le statut des séjours à venir
        $this->createQueryBuilder('s')
            ->update()
            ->set('s.status', ':status')
            ->where('s.entrydate > :now')
            ->setParameter('now', $now)
            ->setParameter('status', 'à venir')
            ->getQuery()
            ->execute();

        // Met à jour le statut des séjours terminés
        $this->createQueryBuilder('s')
            ->update()
            ->set('s.status', ':status')
            ->where('s.leavingdate < :now')
            ->setParameter('now', $now)
            ->setParameter('status', 'terminé')
            ->getQuery()
            ->execute();
    }


    public function findFinishedPaginated(Users $user, int $page, int $limit): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.slot', 'sl') // Jointure avec l'entité Slot
            ->addSelect('sl')
            ->where('a.status = :status')
            ->andWhere('sl.isbooked = true') // Condition pour vérifier si le slot est booké
            ->andWhere('a.user = :user')
            ->setParameter('status', 'terminé')
            ->setParameter('user', $user)
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

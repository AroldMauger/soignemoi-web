<?php

namespace App\Repository;

use App\Entity\Secretary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Secretary>
 */
class SecretaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Secretary::class);
    }

    // Example methods for the SecretaryRepository
}

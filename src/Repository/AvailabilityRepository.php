<?php

namespace App\Repository;

use App\Entity\Availability;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class AvailabilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Availability::class); }
    public function findBookable(): array
    {
        return $this->createQueryBuilder('a')->andWhere('a.active = true')->andWhere('a.startsAt > :now')->setParameter('now', new \DateTimeImmutable())->orderBy('a.startsAt', 'ASC')->getQuery()->getResult();
    }
}

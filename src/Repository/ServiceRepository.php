<?php
namespace App\Repository;

use App\Entity\Service;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Service::class); }
    public function findActive(): array { return $this->findBy(['active' => true], ['displayOrder' => 'ASC']); }
    public function findFeatured(): array { return $this->findBy(['active' => true, 'featured' => true], ['displayOrder' => 'ASC'], 3); }
}

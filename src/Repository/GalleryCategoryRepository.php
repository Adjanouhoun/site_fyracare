<?php

namespace App\Repository;

use App\Entity\GalleryCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class GalleryCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, GalleryCategory::class); }
    /** @return GalleryCategory[] */
    public function findActive(): array { return $this->findBy(['active'=>true], ['displayOrder'=>'ASC','titleFr'=>'ASC']); }
}

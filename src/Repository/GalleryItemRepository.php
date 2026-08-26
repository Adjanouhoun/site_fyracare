<?php

namespace App\Repository;

use App\Entity\GalleryItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class GalleryItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, GalleryItem::class); }
    public function findPublished(): array { return $this->findBy(['active'=>true], ['displayOrder'=>'ASC','id'=>'DESC']); }
    public function findFeatured(int $limit = 3): array { return $this->findBy(['active'=>true,'featured'=>true], ['displayOrder'=>'ASC','id'=>'DESC'], $limit); }
}

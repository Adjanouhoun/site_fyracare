<?php

namespace App\Repository;

use App\Entity\AdviceArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class AdviceArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, AdviceArticle::class); }
    public function findPublished(): array { return $this->findBy(['published'=>true], ['publishedAt'=>'DESC']); }
    public function findFeatured(int $limit = 3): array { return $this->findBy(['published'=>true,'featured'=>true], ['publishedAt'=>'DESC'], $limit); }
    public function findPublishedBySlug(string $slug): ?AdviceArticle { return $this->findOneBy(['slug'=>$slug,'published'=>true]); }
}

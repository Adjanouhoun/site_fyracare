<?php

namespace App\Repository;

use App\Entity\AdviceArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class AdviceArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, AdviceArticle::class); }
    public function findPublished(?string $category = null): array { return $this->findBy(array_filter(['published'=>true, 'category'=>$category]), ['publishedAt'=>'DESC']); }
    public function findFeatured(int $limit = 3): array { return $this->findBy(['published'=>true,'featured'=>true], ['publishedAt'=>'DESC'], $limit); }
    public function findPublishedBySlug(string $slug): ?AdviceArticle { return $this->findOneBy(['slug'=>$slug,'published'=>true]); }
    public function findRelated(AdviceArticle $article, int $limit = 3): array
    {
        return $this->createQueryBuilder('a')->addSelect('CASE WHEN a.category = :category THEN 0 ELSE 1 END AS HIDDEN categoryPriority')->andWhere('a.published = :published')->andWhere('a.id != :id')->setParameter('published', true)->setParameter('id', $article->getId())->setParameter('category', $article->getCategory())->orderBy('categoryPriority', 'ASC')->addOrderBy('a.publishedAt', 'DESC')->setMaxResults($limit)->getQuery()->getResult();
    }
}

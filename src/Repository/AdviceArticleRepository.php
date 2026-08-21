<?php

namespace App\Repository;

use App\Entity\AdviceArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class AdviceArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, AdviceArticle::class); }
    public function findPublished(?string $category = null): array { return $this->findBy(array_filter(['published'=>true, 'category'=>$category]), ['publishedAt'=>'DESC']); }
    public function searchPublished(?string $category, string $search, int $limit, int $offset): array
    {
        $qb = $this->publishedSearchQuery($category, $search);
        return $qb->orderBy('a.publishedAt', 'DESC')->setMaxResults($limit)->setFirstResult($offset)->getQuery()->getResult();
    }
    public function countPublished(?string $category, string $search): int
    {
        return (int) $this->publishedSearchQuery($category, $search)->select('COUNT(a.id)')->getQuery()->getSingleScalarResult();
    }
    public function findFeatured(int $limit = 3): array { return $this->findBy(['published'=>true,'featured'=>true], ['publishedAt'=>'DESC'], $limit); }
    public function findPublishedBySlug(string $slug): ?AdviceArticle { return $this->findOneBy(['slug'=>$slug,'published'=>true]); }
    public function findRelated(AdviceArticle $article, int $limit = 3): array
    {
        return $this->createQueryBuilder('a')->addSelect('CASE WHEN a.category = :category THEN 0 ELSE 1 END AS HIDDEN categoryPriority')->andWhere('a.published = :published')->andWhere('a.id != :id')->setParameter('published', true)->setParameter('id', $article->getId())->setParameter('category', $article->getCategory())->orderBy('categoryPriority', 'ASC')->addOrderBy('a.publishedAt', 'DESC')->setMaxResults($limit)->getQuery()->getResult();
    }
    private function publishedSearchQuery(?string $category, string $search): \Doctrine\ORM\QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')->andWhere('a.published = :published')->setParameter('published', true);
        if ($category) $qb->andWhere('a.category = :category')->setParameter('category', $category);
        if ($search !== '') $qb->andWhere('LOWER(a.titleFr) LIKE :search OR LOWER(a.titleEn) LIKE :search OR LOWER(a.titleAr) LIKE :search OR LOWER(a.excerptFr) LIKE :search OR LOWER(a.excerptEn) LIKE :search OR LOWER(a.excerptAr) LIKE :search')->setParameter('search', '%'.mb_strtolower($search).'%');
        return $qb;
    }
}

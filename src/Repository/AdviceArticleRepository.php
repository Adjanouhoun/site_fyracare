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
        return array_slice($this->matchingPublished($category, $search), $offset, $limit);
    }
    public function countPublished(?string $category, string $search): int
    {
        return count($this->matchingPublished($category, $search));
    }
    public function findFeatured(int $limit = 3): array { return $this->findBy(['published'=>true,'featured'=>true], ['publishedAt'=>'DESC'], $limit); }
    public function findPublishedBySlug(string $slug): ?AdviceArticle { return $this->findOneBy(['slug'=>$slug,'published'=>true]); }
    public function findRelated(AdviceArticle $article, int $limit = 3): array
    {
        return $this->createQueryBuilder('a')->addSelect('CASE WHEN a.category = :category THEN 0 ELSE 1 END AS HIDDEN categoryPriority')->andWhere('a.published = :published')->andWhere('a.id != :id')->setParameter('published', true)->setParameter('id', $article->getId())->setParameter('category', $article->getCategory())->orderBy('categoryPriority', 'ASC')->addOrderBy('a.publishedAt', 'DESC')->setMaxResults($limit)->getQuery()->getResult();
    }
    private function matchingPublished(?string $category, string $search): array
    {
        $articles = $this->findBy(array_filter(['published' => true, 'category' => $category]), ['publishedAt' => 'DESC']);
        if ($search === '') return $articles;
        return array_values(array_filter($articles, fn (AdviceArticle $article): bool => $this->matchesApproximateSearch($article, $search)));
    }
    private function matchesApproximateSearch(AdviceArticle $article, string $search): bool
    {
        $haystack = $this->normalize(implode(' ', [$article->getTitleFr(), $article->getTitleEn(), $article->getTitleAr(), $article->getExcerptFr(), $article->getExcerptEn(), $article->getExcerptAr(), $article->getContentFr(), $article->getContentEn(), $article->getContentAr()]));
        $words = array_values(array_filter(preg_split('/[^\p{L}\p{N}]+/u', $haystack) ?: []));
        $needles = array_values(array_filter(preg_split('/[^\p{L}\p{N}]+/u', $this->normalize($search)) ?: [], static fn (string $word): bool => mb_strlen($word) >= 2));
        foreach ($needles as $needle) {
            $found = false;
            foreach ($words as $word) {
                $distance = levenshtein($needle, $word);
                $tolerance = mb_strlen($needle) >= 7 ? 2 : 1;
                if (str_contains($word, $needle) || str_contains($needle, $word) || $distance <= $tolerance) { $found = true; break; }
            }
            if (!$found) return false;
        }
        return $needles !== [];
    }
    private function normalize(string $value): string
    {
        if (function_exists('transliterator_transliterate')) {
            $normalized = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $value);
            if (is_string($normalized)) return $normalized;
        }
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', mb_strtolower($value));
        return is_string($ascii) ? $ascii : mb_strtolower($value);
    }
}

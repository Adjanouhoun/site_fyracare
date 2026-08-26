<?php

namespace App\Repository;

use App\Entity\SiteContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class SiteContentRepository extends ServiceEntityRepository
{
    private array $byCode = [];
    private bool $loaded = false;
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, SiteContent::class); }
    public function findActiveByCode(string $code): ?SiteContent
    {
        if (!$this->loaded) {
            foreach ($this->findBy(['active' => true]) as $item) $this->byCode[$item->getCode()] = $item;
            $this->loaded = true;
        }
        return $this->byCode[$code] ?? null;
    }
}

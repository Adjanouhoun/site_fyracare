<?php

namespace App\Repository;

use App\Entity\Testimonial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class TestimonialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Testimonial::class); }
    public function findApproved(): array { return $this->findBy(['status' => Testimonial::STATUS_APPROVED], ['createdAt' => 'DESC']); }
}

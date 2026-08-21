<?php

namespace App\Entity;

use App\Repository\TestimonialRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TestimonialRepository::class)]
class Testimonial
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    #[ORM\Id, ORM\GeneratedValue, ORM\Column] private ?int $id = null;
    #[ORM\Column(length: 80), Assert\NotBlank, Assert\Length(max: 80)] private string $author = '';
    #[ORM\Column(length: 150), Assert\NotBlank, Assert\Length(max: 150)] private string $care = '';
    #[ORM\Column(type: Types::TEXT), Assert\NotBlank, Assert\Length(min: 20, max: 1200)] private string $content = '';
    #[ORM\Column, Assert\Range(min: 1, max: 5)] private int $rating = 5;
    #[ORM\Column(length: 20)] private string $status = self::STATUS_PENDING;
    #[ORM\Column] private \DateTimeImmutable $createdAt;

    public function __construct() { $this->createdAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; }
    public function getAuthor(): string { return $this->author; }
    public function setAuthor(string $value): self { $this->author = $value; return $this; }
    public function getCare(): string { return $this->care; }
    public function setCare(string $value): self { $this->care = $value; return $this; }
    public function getContent(): string { return $this->content; }
    public function setContent(string $value): self { $this->content = $value; return $this; }
    public function getRating(): int { return $this->rating; }
    public function setRating(int $value): self { $this->rating = $value; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $value): self { $this->status = $value; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function __toString(): string { return $this->author; }
}

<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ORM\Table(name: 'care_service')]
class Service
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 80, unique: true), Assert\NotBlank, Assert\Regex(pattern: '/^[a-z0-9_]+$/', message: 'Utilisez uniquement des lettres minuscules, chiffres et underscores.')] private string $code = '';
    #[ORM\Column(length: 180), Assert\NotBlank, Assert\Length(max: 180)] private string $titleFr = '';
    #[ORM\Column(length: 180), Assert\NotBlank, Assert\Length(max: 180)] private string $titleEn = '';
    #[ORM\Column(length: 180), Assert\NotBlank, Assert\Length(max: 180)] private string $titleAr = '';
    #[ORM\Column(type: Types::TEXT), Assert\NotBlank] private string $descriptionFr = '';
    #[ORM\Column(type: Types::TEXT), Assert\NotBlank] private string $descriptionEn = '';
    #[ORM\Column(type: Types::TEXT), Assert\NotBlank] private string $descriptionAr = '';
    #[ORM\Column, Assert\PositiveOrZero] private int $displayOrder = 0;
    #[ORM\Column] private bool $active = true;
    #[ORM\Column] private bool $featured = false;

    public function getId(): ?int { return $this->id; }
    public function getCode(): string { return $this->code; }
    public function setCode(string $code): self { $this->code = $code; return $this; }
    public function getTitleFr(): string { return $this->titleFr; }
    public function setTitleFr(string $value): self { $this->titleFr = $value; return $this; }
    public function getTitleEn(): string { return $this->titleEn; }
    public function setTitleEn(string $value): self { $this->titleEn = $value; return $this; }
    public function getTitleAr(): string { return $this->titleAr; }
    public function setTitleAr(string $value): self { $this->titleAr = $value; return $this; }
    public function getDescriptionFr(): string { return $this->descriptionFr; }
    public function setDescriptionFr(string $value): self { $this->descriptionFr = $value; return $this; }
    public function getDescriptionEn(): string { return $this->descriptionEn; }
    public function setDescriptionEn(string $value): self { $this->descriptionEn = $value; return $this; }
    public function getDescriptionAr(): string { return $this->descriptionAr; }
    public function setDescriptionAr(string $value): self { $this->descriptionAr = $value; return $this; }
    public function getDisplayOrder(): int { return $this->displayOrder; }
    public function setDisplayOrder(int $value): self { $this->displayOrder = $value; return $this; }
    public function isActive(): bool { return $this->active; }
    public function setActive(bool $value): self { $this->active = $value; return $this; }
    public function isFeatured(): bool { return $this->featured; }
    public function setFeatured(bool $value): self { $this->featured = $value; return $this; }
    public function getTitle(string $locale): string { return match ($locale) { 'ar' => $this->titleAr, 'en' => $this->titleEn, default => $this->titleFr }; }
    public function getDescription(string $locale): string { return match ($locale) { 'ar' => $this->descriptionAr, 'en' => $this->descriptionEn, default => $this->descriptionFr }; }
    public function __toString(): string { return $this->titleFr; }
}

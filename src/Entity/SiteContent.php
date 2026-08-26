<?php

namespace App\Entity;

use App\Repository\SiteContentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SiteContentRepository::class)]
#[ORM\Table(name: 'site_content')]
#[ORM\UniqueConstraint(name: 'uniq_site_content_code', columns: ['code'])]
#[ORM\HasLifecycleCallbacks]
class SiteContent
{
    public const TYPE_TEXT = 'text';
    public const TYPE_IMAGE = 'image';

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 190), Assert\NotBlank]
    private string $code = '';
    #[ORM\Column(length: 190)]
    private string $label = '';
    #[ORM\Column(length: 80)]
    private string $page = 'general';
    #[ORM\Column(length: 20)]
    private string $type = self::TYPE_TEXT;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contentFr = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contentEn = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contentAr = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;
    #[ORM\Column]
    private bool $active = true;
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct() { $this->updatedAt = new \DateTimeImmutable(); }
    #[ORM\PreUpdate] public function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; }
    public function getCode(): string { return $this->code; } public function setCode(string $v): self { $this->code = $v; return $this; }
    public function getLabel(): string { return $this->label; } public function setLabel(string $v): self { $this->label = $v; return $this; }
    public function getPage(): string { return $this->page; } public function setPage(string $v): self { $this->page = $v; return $this; }
    public function getType(): string { return $this->type; } public function setType(string $v): self { $this->type = $v; return $this; }
    public function getContentFr(): ?string { return $this->contentFr; } public function setContentFr(?string $v): self { $this->contentFr = $v; return $this; }
    public function getContentEn(): ?string { return $this->contentEn; } public function setContentEn(?string $v): self { $this->contentEn = $v; return $this; }
    public function getContentAr(): ?string { return $this->contentAr; } public function setContentAr(?string $v): self { $this->contentAr = $v; return $this; }
    public function getImage(): ?string { return $this->image; } public function setImage(?string $v): self { $this->image = $v; return $this; }
    public function isActive(): bool { return $this->active; } public function setActive(bool $v): self { $this->active = $v; return $this; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function getContent(string $locale): ?string { return match ($locale) { 'ar' => $this->contentAr, 'en' => $this->contentEn, default => $this->contentFr }; }
    public function __toString(): string { return $this->label ?: $this->code; }
}

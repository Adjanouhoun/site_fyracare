<?php

namespace App\Entity;

use App\Repository\AdviceArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdviceArticleRepository::class)]
#[ORM\Table(name: 'advice_article')]
#[ORM\UniqueConstraint(name: 'uniq_advice_slug', columns: ['slug'])]
#[ORM\Index(name: 'idx_advice_publication', columns: ['published', 'published_at'])]
#[ORM\HasLifecycleCallbacks]
class AdviceArticle
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 180), Assert\NotBlank, Assert\Regex(pattern: '/^[a-z0-9-]+$/')]
    private string $slug = '';
    #[ORM\Column(length: 60), Assert\NotBlank]
    private string $category = 'pregnancy';
    #[ORM\Column(length: 180), Assert\NotBlank] private string $titleFr = '';
    #[ORM\Column(length: 180), Assert\NotBlank] private string $titleEn = '';
    #[ORM\Column(length: 180), Assert\NotBlank] private string $titleAr = '';
    #[ORM\Column(type: Types::TEXT), Assert\NotBlank] private string $excerptFr = '';
    #[ORM\Column(type: Types::TEXT), Assert\NotBlank] private string $excerptEn = '';
    #[ORM\Column(type: Types::TEXT), Assert\NotBlank] private string $excerptAr = '';
    #[ORM\Column(type: Types::TEXT), Assert\NotBlank] private string $contentFr = '';
    #[ORM\Column(type: Types::TEXT), Assert\NotBlank] private string $contentEn = '';
    #[ORM\Column(type: Types::TEXT), Assert\NotBlank] private string $contentAr = '';
    #[ORM\Column(length: 180), Assert\NotBlank] private string $seoTitleFr = '';
    #[ORM\Column(length: 180), Assert\NotBlank] private string $seoTitleEn = '';
    #[ORM\Column(length: 180), Assert\NotBlank] private string $seoTitleAr = '';
    #[ORM\Column(length: 320), Assert\NotBlank] private string $seoDescriptionFr = '';
    #[ORM\Column(length: 320), Assert\NotBlank] private string $seoDescriptionEn = '';
    #[ORM\Column(length: 320), Assert\NotBlank] private string $seoDescriptionAr = '';
    #[ORM\Column(length: 160)] private string $author = 'Aminata Boulkheir Diarra';
    #[ORM\Column(length: 255, nullable: true)] private ?string $image = null;
    #[ORM\Column] private bool $published = true;
    #[ORM\Column] private bool $featured = false;
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)] private \DateTimeImmutable $publishedAt;
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)] private \DateTimeImmutable $updatedAt;

    public function __construct() { $this->publishedAt = $this->updatedAt = new \DateTimeImmutable(); }
    #[ORM\PreUpdate] public function touch(): void { $this->updatedAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; }
    public function getSlug(): string { return $this->slug; } public function setSlug(string $v): self { $this->slug=$v; return $this; }
    public function getCategory(): string { return $this->category; } public function setCategory(string $v): self { $this->category=$v; return $this; }
    public function getTitleFr(): string { return $this->titleFr; } public function setTitleFr(string $v): self { $this->titleFr=$v; return $this; }
    public function getTitleEn(): string { return $this->titleEn; } public function setTitleEn(string $v): self { $this->titleEn=$v; return $this; }
    public function getTitleAr(): string { return $this->titleAr; } public function setTitleAr(string $v): self { $this->titleAr=$v; return $this; }
    public function getExcerptFr(): string { return $this->excerptFr; } public function setExcerptFr(string $v): self { $this->excerptFr=$v; return $this; }
    public function getExcerptEn(): string { return $this->excerptEn; } public function setExcerptEn(string $v): self { $this->excerptEn=$v; return $this; }
    public function getExcerptAr(): string { return $this->excerptAr; } public function setExcerptAr(string $v): self { $this->excerptAr=$v; return $this; }
    public function getContentFr(): string { return $this->contentFr; } public function setContentFr(string $v): self { $this->contentFr=$v; return $this; }
    public function getContentEn(): string { return $this->contentEn; } public function setContentEn(string $v): self { $this->contentEn=$v; return $this; }
    public function getContentAr(): string { return $this->contentAr; } public function setContentAr(string $v): self { $this->contentAr=$v; return $this; }
    public function getSeoTitleFr(): string { return $this->seoTitleFr; } public function setSeoTitleFr(string $v): self { $this->seoTitleFr=$v; return $this; }
    public function getSeoTitleEn(): string { return $this->seoTitleEn; } public function setSeoTitleEn(string $v): self { $this->seoTitleEn=$v; return $this; }
    public function getSeoTitleAr(): string { return $this->seoTitleAr; } public function setSeoTitleAr(string $v): self { $this->seoTitleAr=$v; return $this; }
    public function getSeoDescriptionFr(): string { return $this->seoDescriptionFr; } public function setSeoDescriptionFr(string $v): self { $this->seoDescriptionFr=$v; return $this; }
    public function getSeoDescriptionEn(): string { return $this->seoDescriptionEn; } public function setSeoDescriptionEn(string $v): self { $this->seoDescriptionEn=$v; return $this; }
    public function getSeoDescriptionAr(): string { return $this->seoDescriptionAr; } public function setSeoDescriptionAr(string $v): self { $this->seoDescriptionAr=$v; return $this; }
    public function getAuthor(): string { return $this->author; } public function setAuthor(string $v): self { $this->author=$v; return $this; }
    public function getImage(): ?string { return $this->image; } public function setImage(?string $v): self { $this->image=$v; return $this; }
    public function isPublished(): bool { return $this->published; } public function setPublished(bool $v): self { $this->published=$v; return $this; }
    public function isFeatured(): bool { return $this->featured; } public function setFeatured(bool $v): self { $this->featured=$v; return $this; }
    public function getPublishedAt(): \DateTimeImmutable { return $this->publishedAt; } public function setPublishedAt(\DateTimeImmutable $v): self { $this->publishedAt=$v; return $this; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; } public function setUpdatedAt(\DateTimeImmutable $v): self { $this->updatedAt=$v; return $this; }
    public function getTitle(string $locale): string { return match($locale) {'ar'=>$this->titleAr,'en'=>$this->titleEn,default=>$this->titleFr}; }
    public function getExcerpt(string $locale): string { return match($locale) {'ar'=>$this->excerptAr,'en'=>$this->excerptEn,default=>$this->excerptFr}; }
    public function getContent(string $locale): string { return match($locale) {'ar'=>$this->contentAr,'en'=>$this->contentEn,default=>$this->contentFr}; }
    public function getSeoTitle(string $locale): string { return match($locale) {'ar'=>$this->seoTitleAr,'en'=>$this->seoTitleEn,default=>$this->seoTitleFr}; }
    public function getSeoDescription(string $locale): string { return match($locale) {'ar'=>$this->seoDescriptionAr,'en'=>$this->seoDescriptionEn,default=>$this->seoDescriptionFr}; }
    public function __toString(): string { return $this->titleFr; }
}

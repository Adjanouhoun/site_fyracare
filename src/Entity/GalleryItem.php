<?php

namespace App\Entity;

use App\Repository\GalleryItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GalleryItemRepository::class)]
#[ORM\Table(name: 'gallery_item')]
#[ORM\Index(name: 'IDX_GALLERY_ITEM_CATEGORY', columns: ['category_id'])]
class GalleryItem
{
    public const TYPE_IMAGE = 'image';
    public const TYPE_VIDEO = 'video';
    #[ORM\Id, ORM\GeneratedValue, ORM\Column] private ?int $id = null;
    #[ORM\Column(length: 20)] private string $type = self::TYPE_IMAGE;
    #[ORM\ManyToOne(targetEntity: GalleryCategory::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?GalleryCategory $category = null;
    #[ORM\Column(length: 180)] private string $titleFr = '';
    #[ORM\Column(length: 180)] private string $titleEn = '';
    #[ORM\Column(length: 180)] private string $titleAr = '';
    #[ORM\Column(type: Types::TEXT, nullable: true)] private ?string $captionFr = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)] private ?string $captionEn = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)] private ?string $captionAr = null;
    #[ORM\Column(length: 255, nullable: true)] private ?string $mediaFile = null;
    #[ORM\Column(length: 500, nullable: true)] private ?string $videoUrl = null;
    #[ORM\Column(length: 255, nullable: true)] private ?string $thumbnail = null;
    #[ORM\Column] private int $displayOrder = 0;
    #[ORM\Column] private bool $active = true;
    #[ORM\Column] private bool $featured = false;
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)] private \DateTimeImmutable $createdAt;
    public function __construct() { $this->createdAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; }
    public function getType(): string { return $this->type; } public function setType(string $v): self { $this->type=$v; return $this; }
    public function getCategory(): ?GalleryCategory { return $this->category; } public function setCategory(?GalleryCategory $v): self { $this->category=$v; return $this; }
    public function getTitleFr(): string { return $this->titleFr; } public function setTitleFr(string $v): self { $this->titleFr=$v; return $this; }
    public function getTitleEn(): string { return $this->titleEn; } public function setTitleEn(string $v): self { $this->titleEn=$v; return $this; }
    public function getTitleAr(): string { return $this->titleAr; } public function setTitleAr(string $v): self { $this->titleAr=$v; return $this; }
    public function getCaptionFr(): ?string { return $this->captionFr; } public function setCaptionFr(?string $v): self { $this->captionFr=$v; return $this; }
    public function getCaptionEn(): ?string { return $this->captionEn; } public function setCaptionEn(?string $v): self { $this->captionEn=$v; return $this; }
    public function getCaptionAr(): ?string { return $this->captionAr; } public function setCaptionAr(?string $v): self { $this->captionAr=$v; return $this; }
    public function getMediaFile(): ?string { return $this->mediaFile; } public function setMediaFile(?string $v): self { $this->mediaFile=$v; return $this; }
    public function getVideoUrl(): ?string { return $this->videoUrl; } public function setVideoUrl(?string $v): self { $this->videoUrl=$v; return $this; }
    public function getThumbnail(): ?string { return $this->thumbnail; } public function setThumbnail(?string $v): self { $this->thumbnail=$v; return $this; }
    public function getDisplayOrder(): int { return $this->displayOrder; } public function setDisplayOrder(int $v): self { $this->displayOrder=$v; return $this; }
    public function isActive(): bool { return $this->active; } public function setActive(bool $v): self { $this->active=$v; return $this; }
    public function isFeatured(): bool { return $this->featured; } public function setFeatured(bool $v): self { $this->featured=$v; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getTitle(string $locale): string { return match($locale) {'ar'=>$this->titleAr,'en'=>$this->titleEn,default=>$this->titleFr}; }
    public function getCaption(string $locale): ?string { return match($locale) {'ar'=>$this->captionAr,'en'=>$this->captionEn,default=>$this->captionFr}; }
    public function getEmbedUrl(): ?string
    {
        if (!$this->videoUrl) return null;
        if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([A-Za-z0-9_-]+)~', $this->videoUrl, $m)) return 'https://www.youtube-nocookie.com/embed/'.$m[1];
        if (preg_match('~vimeo\.com/(\d+)~', $this->videoUrl, $m)) return 'https://player.vimeo.com/video/'.$m[1];
        return $this->videoUrl;
    }
    public function __toString(): string { return $this->titleFr; }
}

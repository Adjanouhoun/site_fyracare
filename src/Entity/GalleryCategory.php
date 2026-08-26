<?php

namespace App\Entity;

use App\Repository\GalleryCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GalleryCategoryRepository::class)]
#[ORM\Table(name: 'gallery_category')]
#[ORM\UniqueConstraint(name: 'UNIQ_GALLERY_CATEGORY_SLUG', columns: ['slug'])]
class GalleryCategory
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column] private ?int $id = null;
    #[ORM\Column(length: 120)] private string $slug = '';
    #[ORM\Column(length: 180)] private string $titleFr = '';
    #[ORM\Column(length: 180)] private string $titleEn = '';
    #[ORM\Column(length: 180)] private string $titleAr = '';
    #[ORM\Column(type: Types::TEXT, nullable: true)] private ?string $descriptionFr = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)] private ?string $descriptionEn = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)] private ?string $descriptionAr = null;
    #[ORM\Column] private int $displayOrder = 0;
    #[ORM\Column] private bool $active = true;
    /** @var Collection<int, GalleryItem> */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: GalleryItem::class)] private Collection $items;

    public function __construct() { $this->items = new ArrayCollection(); }
    public function getId(): ?int { return $this->id; }
    public function getSlug(): string { return $this->slug; } public function setSlug(string $v): self { $this->slug=$v; return $this; }
    public function getTitleFr(): string { return $this->titleFr; } public function setTitleFr(string $v): self { $this->titleFr=$v; return $this; }
    public function getTitleEn(): string { return $this->titleEn; } public function setTitleEn(string $v): self { $this->titleEn=$v; return $this; }
    public function getTitleAr(): string { return $this->titleAr; } public function setTitleAr(string $v): self { $this->titleAr=$v; return $this; }
    public function getDescriptionFr(): ?string { return $this->descriptionFr; } public function setDescriptionFr(?string $v): self { $this->descriptionFr=$v; return $this; }
    public function getDescriptionEn(): ?string { return $this->descriptionEn; } public function setDescriptionEn(?string $v): self { $this->descriptionEn=$v; return $this; }
    public function getDescriptionAr(): ?string { return $this->descriptionAr; } public function setDescriptionAr(?string $v): self { $this->descriptionAr=$v; return $this; }
    public function getDisplayOrder(): int { return $this->displayOrder; } public function setDisplayOrder(int $v): self { $this->displayOrder=$v; return $this; }
    public function isActive(): bool { return $this->active; } public function setActive(bool $v): self { $this->active=$v; return $this; }
    public function getItems(): Collection { return $this->items; }
    public function getTitle(string $locale): string { return match($locale) {'ar'=>$this->titleAr ?: $this->titleFr,'en'=>$this->titleEn ?: $this->titleFr,default=>$this->titleFr}; }
    public function getDescription(string $locale): ?string { return match($locale) {'ar'=>$this->descriptionAr ?: $this->descriptionFr,'en'=>$this->descriptionEn ?: $this->descriptionFr,default=>$this->descriptionFr}; }
    public function __toString(): string { return $this->titleFr; }
}

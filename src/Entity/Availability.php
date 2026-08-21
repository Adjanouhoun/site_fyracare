<?php

namespace App\Entity;

use App\Repository\AvailabilityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvailabilityRepository::class)]
class Availability
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column] private ?int $id = null;
    #[ORM\Column] private \DateTimeImmutable $startsAt;
    #[ORM\Column] private bool $active = true;

    public function __construct() { $this->startsAt = new \DateTimeImmutable('+1 day 09:00'); }
    public function getId(): ?int { return $this->id; }
    public function getStartsAt(): \DateTimeImmutable { return $this->startsAt; }
    public function setStartsAt(\DateTimeImmutable $value): self { $this->startsAt = $value; return $this; }
    public function isActive(): bool { return $this->active; }
    public function setActive(bool $value): self { $this->active = $value; return $this; }
    public function __toString(): string { return $this->startsAt->format('d/m/Y · H:i'); }
}

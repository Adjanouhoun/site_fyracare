<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
class Appointment
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    #[ORM\Id, ORM\GeneratedValue, ORM\Column] private ?int $id = null;
    #[ORM\ManyToOne, ORM\JoinColumn(nullable: false)] private ?Service $service = null;
    #[ORM\ManyToOne, ORM\JoinColumn(nullable: false)] private ?Availability $availability = null;
    #[ORM\Column(length: 120), Assert\NotBlank, Assert\Length(max: 120)] private string $fullName = '';
    #[ORM\Column(length: 30), Assert\NotBlank, Assert\Length(max: 30)] private string $phone = '';
    #[ORM\Column(length: 180, nullable: true), Assert\Email] private ?string $email = null;
    #[ORM\Column(type: Types::TEXT, nullable: true), Assert\Length(max: 1200)] private ?string $note = null;
    #[ORM\Column(length: 20)] private string $status = self::STATUS_PENDING;
    #[ORM\Column] private \DateTimeImmutable $createdAt;

    public function __construct() { $this->createdAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; }
    public function getService(): ?Service { return $this->service; }
    public function setService(?Service $value): self { $this->service = $value; return $this; }
    public function getAvailability(): ?Availability { return $this->availability; }
    public function setAvailability(?Availability $value): self { $this->availability = $value; return $this; }
    public function getFullName(): string { return $this->fullName; }
    public function setFullName(string $value): self { $this->fullName = $value; return $this; }
    public function getPhone(): string { return $this->phone; }
    public function setPhone(string $value): self { $this->phone = $value; return $this; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $value): self { $this->email = $value ?: null; return $this; }
    public function getNote(): ?string { return $this->note; }
    public function setNote(?string $value): self { $this->note = $value ?: null; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $value): self { $this->status = $value; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function __toString(): string { return $this->fullName.' · '.($this->service?->getTitleFr() ?? ''); }
}

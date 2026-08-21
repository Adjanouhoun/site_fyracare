<?php

namespace App\Entity;

use App\Repository\ContactMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactMessageRepository::class)]
class ContactMessage
{
    public const STATUS_NEW = 'new';
    public const STATUS_PROCESSED = 'processed';
    #[ORM\Id, ORM\GeneratedValue, ORM\Column] private ?int $id = null;
    #[ORM\Column(length: 120), Assert\NotBlank, Assert\Length(max: 120)] private string $name = '';
    #[ORM\Column(length: 180), Assert\NotBlank, Assert\Email] private string $email = '';
    #[ORM\Column(length: 30, nullable: true)] private ?string $phone = null;
    #[ORM\Column(length: 180), Assert\NotBlank, Assert\Length(max: 180)] private string $subject = '';
    #[ORM\Column(type: Types::TEXT), Assert\NotBlank, Assert\Length(min: 10, max: 3000)] private string $message = '';
    #[ORM\Column(length: 20)] private string $status = self::STATUS_NEW;
    #[ORM\Column] private \DateTimeImmutable $createdAt;
    public function __construct() { $this->createdAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; } public function setName(string $v): self { $this->name=$v; return $this; }
    public function getEmail(): string { return $this->email; } public function setEmail(string $v): self { $this->email=$v; return $this; }
    public function getPhone(): ?string { return $this->phone; } public function setPhone(?string $v): self { $this->phone=$v ?: null; return $this; }
    public function getSubject(): string { return $this->subject; } public function setSubject(string $v): self { $this->subject=$v; return $this; }
    public function getMessage(): string { return $this->message; } public function setMessage(string $v): self { $this->message=$v; return $this; }
    public function getStatus(): string { return $this->status; } public function setStatus(string $v): self { $this->status=$v; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function __toString(): string { return $this->name.' · '.$this->subject; }
}

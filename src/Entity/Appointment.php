<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Auth\User;
use App\Enum\AppointmentStatusEnum;
use App\Enum\StatusEnum;
use App\Repository\AppointmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use Symfony\Component\Serializer\Annotation\Groups;

$APPOINTMENT_STATUS = AppointmentStatusEnum::getValues();

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => 'appointment-read']),
        new Post(
            normalizationContext: ['groups' => 'appointment-read'],
            denormalizationContext: ['groups' => 'appointment-write'],
        ),
        new Get(normalizationContext: ['groups' => 'appointment-read']),
        new Patch(
            normalizationContext: ['groups' => 'appointment-read'],
            denormalizationContext: ['groups' => 'appointment-update'],
        ),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['establishment' => 'exact'])]
class Appointment
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['appointment-read', 'appointment-read'])]
    protected UuidInterface|string $id;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['appointment-read', 'appointment-read', 'appointment-write'])]
    private ?Barber $barber = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['appointment-read', 'appointment-read', 'appointment-write'])]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['appointment-read', 'appointment-read', 'appointment-write'])]
    private ?Service $service = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice([StatusEnum::FINISHED->value, StatusEnum::PLANNED->value, StatusEnum::CANCELED->value])]
    #[Groups(['appointment-read', 'appointment-read', 'appointment-update'])]
    private ?string $status = StatusEnum::PLANNED->value;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Type(type: \DateTimeInterface::class)]
    #[Groups(['appointment-read', 'appointment-read', 'appointment-write', 'appointment-update'])]
    #[Context(normalizationContext: [DateTimeNormalizer::class])]
    private ?\DateTimeInterface $dateTime = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['appointment-read', 'appointment-read', 'appointment-write'])]
    private ?Establishment $establishment = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getBarber(): ?Barber
    {
        return $this->barber;
    }

    public function setBarber(?Barber $barber): static
    {
        $this->barber = $barber;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): static
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getEstablishment(): ?Establishment
    {
        return $this->establishment;
    }

    public function setEstablishment(?Establishment $establishment): static
    {
        $this->establishment = $establishment;

        return $this;
    }
}

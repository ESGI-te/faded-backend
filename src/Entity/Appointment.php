<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Auth\User;
use App\Enum\AppointmentStatusEnum;
use App\Enum\StatusEnum;
use App\Repository\AppointmentRepository;
use App\State\CreateAppointmentProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
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
use App\Validator\Constraints\DateTimeAfterNow;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => 'appointment-read'],
            security: "is_granted('ROLE_USER')"
        ),
        new GetCollection(
            uriTemplate: '/appointments/establishment',
            normalizationContext: ['groups' => 'appointment-establishment-read'],
        ),
        new Post(
            normalizationContext: ['groups' => 'appointment-read'],
            denormalizationContext: ['groups' => 'appointment-write'],
            security: "is_granted('ROLE_USER')",
            validationContext: ['groups' => 'appointment-write'],
            processor: CreateAppointmentProcessor::class,
        ),
        new Get(
            normalizationContext: ['groups' => 'appointment-read'],
            security:
            "is_granted('ROLE_USER') and object.getUser() == user 
            or is_granted('ROLE_PROVIDER') and object.getEstablishment().getProvider().getUser() == user
            or is_granted('ROLE_ADMIN')"
        ),
        new Patch(
            normalizationContext: ['groups' => 'appointment-read'],
            denormalizationContext: ['groups' => 'appointment-postpone'],
            security: "is_granted('ROLE_USER') and object.getUser() == user",
        ),
        new Patch(
            uriTemplate: '/appointments/{id}/cancel',
            normalizationContext: ['groups' => 'appointment-read'],
            denormalizationContext: ['groups' => 'appointment-cancel'],
            security: "is_granted('ROLE_USER') and object.getUser() == user",
            validationContext: ['groups' => 'appointment-cancel'],
        ),
        new Patch(
            uriTemplate: '/appointments/{id}/complete',
            normalizationContext: ['groups' => 'appointment-read'],
            denormalizationContext: ['groups' => 'appointment-complete'],
            security: "is_granted('ROLE_USER')",
            validationContext: ['groups' => 'appointment-complete'],
        ),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['establishment' => 'exact'])]
class Appointment
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[Groups(['appointment-read'])]
    protected UuidInterface|string $id;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['appointment-read', 'appointment-postpone', 'appointment-write', 'appointment-establishment-read'])]
    private ?Barber $barber = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['appointment-read', 'appointment-write'])]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['appointment-read', 'appointment-write', 'appointment-establishment-read'])]
    private ?Service $service = null;

    #[ORM\Column(length: 255)]
    #[Assert\EqualTo(value: AppointmentStatusEnum::CANCELED->value, groups: ['appointment-cancel'])]
    #[Assert\EqualTo(value: AppointmentStatusEnum::FINISHED->value, groups: ['appointment-complete'])]
    #[Groups(['appointment-read', 'appointment-cancel', 'appointment-complete'])]
    private ?string $status = StatusEnum::PLANNED->value;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups([
        'appointment-read',
        'appointment-write',
        'appointment-postpone',
        'appointment-cancel',
        'appointment-establishment-read'])]
    #[Context(normalizationContext: [DateTimeNormalizer::class])]
    #[Assert\Type(type: \DateTimeInterface::class)]
    #[DateTimeAfterNow(groups: ['appointment-postpone', 'appointment-cancel', 'appointment-write'])]
    private ?\DateTimeInterface $dateTime = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['appointment-read', 'appointment-write'])]
    private ?Establishment $establishment = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    private ?Provider $provider = null;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

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

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): static
    {
        $this->provider = $provider;

        return $this;
    }
}

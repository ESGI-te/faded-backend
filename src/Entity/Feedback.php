<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\Auth\User;
use App\Repository\FeedbackRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(
             security: "is_granted('ROLE_USER')",
        )
    ],
    normalizationContext: ['groups' => 'feedback-read'],
)
]
#[ApiFilter(SearchFilter::class, properties: ['establishment' => 'exact'])]
class Feedback
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['establishment-read', 'feedback-read'])]
    protected UuidInterface|string $id;

    #[ORM\ManyToOne(inversedBy: 'feedback')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['feedback-read'])]
    private ?Provider $provider = null;

    #[ORM\ManyToOne(inversedBy: 'feedback')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['feedback-read'])]
    private ?Establishment $establishment = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['establishment-read', 'feedback-read'])]
    private ?\DateTimeInterface $date_time = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['feedback-read'])]
    private ?Service $service = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['establishment-read', 'feedback-read'])]
    #[Assert\Length(min: 10, max: 255)]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'feedback')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['establishment-read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'feedback')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Groups(['feedback-read'])]
    private ?Barber $barber = null;

    #[ORM\Column]
    #[Groups(['establishment-read', 'feedback-read'])]
    #[Assert\Type(type: 'integer')]
    #[Assert\Range([
        'min' => 0,
        'max' => 5,
    ])]
    private ?int $note = null;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getEstablishment(): ?Establishment
    {
        return $this->establishment;
    }

    public function setEstablishment(?Establishment $establishment): static
    {
        $this->establishment = $establishment;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->date_time;
    }

    public function setDateTime(\DateTimeInterface $date_time): static
    {
        $this->date_time = $date_time;

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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

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

    public function getBarber(): ?Barber
    {
        return $this->barber;
    }

    public function setBarber(?Barber $barber): static
    {
        $this->barber = $barber;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

        return $this;
    }
}

<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use App\Entity\Auth\User;
use App\Repository\BarberRepository;
use App\State\BarberPlanningProcessor;
use App\State\CreateBarberProcessor;
use App\Utils\Constants;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Validator\Constraints\Planning;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BarberRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => 'barber-read'],
            security: "is_granted('ROLE_ADMIN') 
            or is_granted('ROLE_PROVIDER') and object.getProvider().getUser() == user"
        ),
        new GetCollection(normalizationContext: ['groups' => 'barber-read']),
        new Post(
            normalizationContext: ['groups' => 'barber-read'],
            denormalizationContext: ['groups' => 'barber-write'],
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_PROVIDER')",
            processor: CreateBarberProcessor::class,
        ),
        new Patch(
            normalizationContext: ['groups' => 'barber-read'],
            denormalizationContext: ['groups' => 'barber-update'],
            security: "is_granted('ROLE_PROVIDER') and object.getProvider().getUser() == user",
            processor: BarberPlanningProcessor::class,
        ),
        new Patch(
            uriTemplate: '/barbers/{id}/planning',
            normalizationContext: ['groups' => 'barber-read'],
            denormalizationContext: ['groups' => 'barber-update-planning'],
            security: "is_granted('ROLE_PROVIDER') and object.getProvider().getUser() == user",
            validationContext: ['groups' => ['barber-update-planning']],
        ),
        new Delete(
            normalizationContext: ['groups' => 'barber-delete'],
            security: "
            is_granted('ROLE_ADMIN') 
            or is_granted('ROLE_PROVIDER') and object.getProvider().getUser() == user"
        ),
    ]
)]
#[ApiFilter(ExistsFilter::class, properties: ['establishment'])]
#[ApiFilter(SearchFilter::class, properties: [
    'establishment' => 'exact',
    'lastName' => 'ipartial',
])]
class Barber
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups([
        'establishment-read',
        'appointment-read',
        'barber-read',
        'user-read-barber',
        'barber-delete',
        'feedback-read',
    ])]
    protected UuidInterface|string $id;

    #[ORM\Column(length: 255)]
    #[Groups(['establishment-read', 'appointment-read', 'barber-read', 'barber-write', 'barber-update','feedback-read'])]
    #[Assert\Length(min: 2, max: 80)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['establishment-read', 'appointment-read', 'barber-read', 'barber-write', 'barber-update','feedback-read'])]
    #[Assert\Length(min: 2, max: 80)]
    private ?string $lastName = null;

    #[ORM\ManyToOne(inversedBy: 'barbers')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['barber-read', 'barber-update'])]
    private ?Establishment $establishment = null;

    #[ORM\ManyToOne(inversedBy: 'barbers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['barber-read'])]
    private ?Provider $provider = null;

    #[ORM\OneToMany(mappedBy: 'barber', targetEntity: Appointment::class, orphanRemoval: true)]
    private Collection $appointments;

    #[ORM\OneToMany(mappedBy: 'barber', targetEntity: Feedback::class)]
    private Collection $feedback;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['barber-image-read'])]
    private ?Image $image = null;

    #[ORM\Column]
    #[ApiProperty(
        openapiContext: [
            'type' => 'object',
            'example' => [
                'monday' => [
                    'open' => '08:00',
                    'close' => '12:00',
                ],
                'tuesday' => [
                    'open' => '08:00',
                    'close' => '12:00',
                ],
                'wednesday' => [
                    'open' => '08:00',
                    'close' => '12:00',
                ],
                'thursday' => [
                    'open' => '08:00',
                    'close' => '12:00',
                ],
                'friday' => [
                    'open' => '08:00',
                    'close' => '12:00',
                ],
                'saturday' => [
                    'open' => '08:00',
                    'close' => '12:00',
                ],
                'sunday' => [
                    'open' => '08:00',
                    'close' => '12:00',
                ],
            ]
        ]
    )]
    #[Groups(['barber-read', 'barber-update', 'barber-write'])]
    #[Assert\NotNull(groups: ['barber-update', 'barber-write'])]
    #[Planning(groups: ['barber-update', 'barber-write'])]
    private ?array $planning = null;

    #[ORM\OneToOne(mappedBy: 'barber', cascade: ['persist', 'remove'])]
    #[Groups(['barber-read'])]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Groups(['barber-read', 'barber-update', 'barber-write'])]
    #[Assert\Email]
    private ?string $email = null;

    public function __construct()
    {
        $this->appointments = new ArrayCollection();
        $this->feedback = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

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

    /**
     * @return Collection<int, Appointment>
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): static
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments->add($appointment);
            $appointment->setBarber($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): static
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getBarber() === $this) {
                $appointment->setBarber(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Feedback>
     */
    public function getFeedback(): Collection
    {
        return $this->feedback;
    }

    public function addFeedback(Feedback $feedback): static
    {
        if (!$this->feedback->contains($feedback)) {
            $this->feedback->add($feedback);
            $feedback->setBarber($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): static
    {
        if ($this->feedback->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getBarber() === $this) {
                $feedback->setBarber(null);
            }
        }

        return $this;
    }


    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getPlanning(): array
    {
        return $this->planning;
    }

    public function setPlanning(array $planning): static
    {
        $this->planning = $planning;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setBarber(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getBarber() !== $this) {
            $user->setBarber($this);
        }

        $this->user = $user;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
}

<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Repository\BarberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Controller\UploadBarberImageController;

#[ORM\Entity(repositoryClass: BarberRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/barbers/{id}/images/upload',
            controller: UploadBarberImageController::class,
            name: 'barber_image_upload',
            deserialize: false,
            normalizationContext: [
                'groups' => ['barber-image-write']
            ],
        ),
        new Post(
            normalizationContext: ['groups' => 'barber-write-read'],
            denormalizationContext: ['groups' => 'barber-write'],
        ),
        new Get(
            uriTemplate: '/barbers/{id}/images',
            normalizationContext: ['groups' => 'barber-image-read']
        ),
        new GetCollection(),
        new Patch(),
    ]
)]
class Barber
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['establishment-read'])]
    protected UuidInterface|string $id;

    #[ORM\Column(length: 255)]
    #[Groups(['establishment-read'])]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['establishment-read'])]
    private ?string $last_name = null;

    #[ORM\ManyToOne(inversedBy: 'barbers')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Establishment $establishment = null;

    #[ORM\ManyToOne(inversedBy: 'barbers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Provider $provider = null;

    #[ORM\OneToMany(mappedBy: 'barber', targetEntity: Appointment::class, orphanRemoval: true)]
    private Collection $appointments;

    #[ORM\OneToMany(mappedBy: 'barber', targetEntity: Feedback::class)]
    private Collection $feedback;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['barber-image-read'])]
    private ?Image $image = null;

    #[ORM\Column]
    #[Groups(['barber-read', 'barber-write-read', 'barber-write'])]
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
    private array $planning = [];

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
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

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
}

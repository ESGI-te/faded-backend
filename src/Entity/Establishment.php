<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\EstablishmentStatusEnum;
use App\Filter\EstablishmentFilter;
use App\Repository\EstablishmentRepository;
use App\State\EstablishmentStatusProcessor;
use App\Utils\Constants;
use App\Validator\Constraints\Planning;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EstablishmentRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/establishments/search',
            normalizationContext: ['groups' => 'establishment-search-read']
        ),
        new GetCollection(
            security: "is_granted('ROLE_PROVIDER') or is_granted('ROLE_ADMIN')",
        ),
        new GetCollection(
            uriTemplate: '/establishments/suggestions',
            normalizationContext: ['groups' => 'establishment-suggestion']
        ),
        new Patch(
            normalizationContext: ['groups' => 'establishment-write-read'],
            denormalizationContext: ['groups' => 'establishment-write'],
            security: "is_granted('ROLE_PROVIDER') and object.getProvider().getUser() == user",
            validationContext: ['groups' => 'establishment-update'],
        ),
        new Patch(
            uriTemplate: '/establishments/{id}/status',
            normalizationContext: ['groups' => 'establishment-read'],
            denormalizationContext: ['groups' => 'establishment-write-status'],
            security: "is_granted('ROLE_PROVIDER') and object.getProvider().getUser() == user",
            processor: EstablishmentStatusProcessor::class,
        ),
        new Post(
            normalizationContext: ['groups' => 'establishment-write-read'],
            denormalizationContext: ['groups' => 'establishment-write'],
            security: "is_granted('ROLE_PROVIDER')",
            validationContext: ['groups' => 'establishment-write'],
            processor: 'App\State\AddProviderProcessor',
        ),
        new Get(normalizationContext: ['groups' => 'establishment-read']),
        new Get(
            uriTemplate: '/establishments/{id}/images',
            normalizationContext: ['groups' => 'establishment-images-read']
        ),
        new Delete(security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_PROVIDER') and object.getProvider().getUser() == user"),
    ])]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'ipartial',
])]
#[ApiFilter(EstablishmentFilter::class, properties:
[
    'address' => 'partial',
    'serviceCategories' => 'exact',
])]
class Establishment
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['establishment-suggestion', 'establishment-read', 'establishment-write-read', 'appointment-read', 'barber-read', 'establishment-search-read', 'service-read'])]
    protected UuidInterface|string $id;

    #[ORM\Column(length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'establishment-suggestion',
        'establishment-read',
        'establishment-write-read',
        'establishment-write',
        'appointment-read',
        'barber-read',
        'establishment-search-read',
        'establishment-update'
    ])]
    #[Assert\Length(min: 2, max: 120, groups: ['establishment-write'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'establishments')]
    #[Groups(['establishment-read', 'establishment-write-read'])]
    private ?Provider $provider = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['establishment-read', 'establishment-update'])]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['establishment-read', 'establishment-update'])]
    #[Assert\Regex(pattern: '/^\+?[1-9][0-9]{7,14}$/')]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['establishment-read', 'establishment-update', 'appointment-read', 'establishment-search-read'])]
    #[Assert\Length(min: 5)]
    private ?string $address = null;

    #[ORM\ManyToMany(targetEntity: Service::class, mappedBy: 'establishment')]
    #[Groups(['establishment-read', 'establishment-update'])]
    private Collection $services;

    #[ORM\OneToMany(mappedBy: 'establishment', targetEntity: Barber::class)]
    #[Groups(['establishment-read', 'establishment-update'])]
    private Collection $barbers;

    #[ORM\OneToMany(mappedBy: 'establishment', targetEntity: Feedback::class, orphanRemoval: true)]
    #[Groups(['establishment-read'])]
    private Collection $feedback;

    #[ORM\Column(nullable: true)]
    #[Groups(['establishment-read', 'establishment-search-read'])]
    #[Assert\Type(type: 'float')]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['establishment-read', 'establishment-search-read'])]
    #[Assert\Type(type: 'float')]
    private ?float $longitude = null;

    #[ORM\ManyToMany(targetEntity: ServiceCategory::class, mappedBy: 'establishment')]
    #[Groups(['establishment-read', 'establishment-update'])]
    private Collection $serviceCategories;

    #[ORM\OneToMany(mappedBy: 'establishment', targetEntity: Image::class)]
    #[Groups(['establishment-image-read'])]
    private Collection $images;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['establishment-read', 'establishment-update'])]
    #[ApiProperty(
        openapiContext: [
            'type' => 'object',
            'example' => Constants::DEFAULT_PLANNING,
        ]
    )]
    #[Planning]
    private array $planning = Constants::DEFAULT_PLANNING;

    #[ORM\OneToMany(mappedBy: 'establishment', targetEntity: Appointment::class, orphanRemoval: true)]
    private Collection $appointments;

    #[ORM\Column(length: 255)]
    #[Groups(['establishment-read', 'establishment-write-read', 'establishment-update'])]
    #[Assert\Choice(callback: [EstablishmentStatusEnum::class, 'getValues'], groups: ['establishment-update'])]
    private ?string $status = EstablishmentStatusEnum::DRAFT->value;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cover = null;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->barbers = new ArrayCollection();
        $this->feedback = new ArrayCollection();
        $this->serviceCategories = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->appointments = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->addEstablishment($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            $service->removeEstablishment($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Barber>
     */
    public function getBarbers(): Collection
    {
        return $this->barbers;
    }

    public function addBarber(Barber $barber): static
    {
        if (!$this->barbers->contains($barber)) {
            $this->barbers->add($barber);
            $barber->setEstablishment($this);
        }

        return $this;
    }

    public function removeBarber(Barber $barber): static
    {
        if ($this->barbers->removeElement($barber)) {
            // set the owning side to null (unless already changed)
            if ($barber->getEstablishment() === $this) {
                $barber->setEstablishment(null);
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
            $feedback->setEstablishment($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): static
    {
        if ($this->feedback->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getEstablishment() === $this) {
                $feedback->setEstablishment(null);
            }
        }

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection<int, ServiceCategory>
     */
    public function getServiceCategories(): Collection
    {
        return $this->serviceCategories;
    }

    public function addServiceCategory(ServiceCategory $serviceCategory): static
    {
        if (!$this->serviceCategories->contains($serviceCategory)) {
            $this->serviceCategories->add($serviceCategory);
            $serviceCategory->addEstablishment($this);
        }

        return $this;
    }

    public function removeServiceCategory(ServiceCategory $serviceCategory): static
    {
        if ($this->serviceCategories->removeElement($serviceCategory)) {
            $serviceCategory->removeEstablishment($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setEstablishment($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getEstablishment() === $this) {
                $image->setEstablishment(null);
            }
        }

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
            $appointment->setEstablishment($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): static
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getEstablishment() === $this) {
                $appointment->setEstablishment(null);
            }
        }

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

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): static
    {
        $this->cover = $cover;

        return $this;
    }
}

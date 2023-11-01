<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\GeoLocalisationController;
use App\Controller\UserInfoController;
use App\Repository\EstablishmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EstablishmentRepository::class)]
#[ApiResource(operations: [
    new GetCollection(normalizationContext: ['groups' => 'search-info']),
    new Post(),
    new Get(),
    new GetCollection(
        name: 'search-info',
        uriTemplate: '/establishements/search',
        controller: GeoLocalisationController::class,
        security: "is_granted('ROLE_USER')",

    ),
    new Patch( ),
    new Delete(security: "is_granted('ROLE_ADMIN')"),])]
#[ApiFilter(SearchFilter::class,properties: ['name'=>'partial'])]
class Establishment
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['search-info'])]
    protected UuidInterface|string $id;


    #[ORM\Column(length: 255)]
    #[Groups(['search-info'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'establishments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Provider $provider = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\ManyToMany(targetEntity: Service::class, mappedBy: 'establishment')]
    private Collection $services;

    #[ORM\OneToMany(mappedBy: 'establishment', targetEntity: Barber::class)]
    private Collection $barbers;

    #[ORM\OneToMany(mappedBy: 'establishment', targetEntity: Feedback::class, orphanRemoval: true)]
    private Collection $feedback;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?WeeklyOpeningHours $opening_hours = null;

    #[ORM\Column]
    private ?float $latitude = null;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\ManyToMany(targetEntity: ServiceCategory::class, mappedBy: 'establishement')]
    private Collection $serviceCategories;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->barbers = new ArrayCollection();
        $this->feedback = new ArrayCollection();
        $this->serviceCategories = new ArrayCollection();
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

    public function getOpeningHours(): ?WeeklyOpeningHours
    {
        return $this->opening_hours;
    }

    public function setOpeningHours(WeeklyOpeningHours $opening_hours): static
    {
        $this->opening_hours = $opening_hours;

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
            $serviceCategory->addEstablishement($this);
        }

        return $this;
    }

    public function removeServiceCategory(ServiceCategory $serviceCategory): static
    {
        if ($this->serviceCategories->removeElement($serviceCategory)) {
            $serviceCategory->removeEstablishement($this);
        }

        return $this;
    }
}

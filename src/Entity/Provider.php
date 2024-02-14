<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\UploadProviderImageController;
use App\Entity\Auth\User;
use App\Repository\ProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Get(
            security: "
            is_granted('ROLE_ADMIN') 
            or is_granted('ROLE_PROVIDER') and object.getUser() == user"
        ),
        new Post(
            denormalizationContext: ['groups' => ['provider-create']],
            security: "is_granted('ROLE_ADMIN')",
            validationContext: ['groups' => ['provider-create']],
        ),
        new Patch(
            denormalizationContext: ['groups' => ['provider-update']],
            security: "is_granted('ROLE_PROVIDER') and object.getUser() == user"
        ),
        new Patch(
            uriTemplate: '/providers/{id}/image',
            denormalizationContext: ['groups' => ['provider-update-image']],
            security: "is_granted('ROLE_PROVIDER') and object.getUser() == user",
        ),
        new Delete(
            security: "
            is_granted('ROLE_ADMIN') 
            or is_granted('ROLE_PROVIDER') and object.getUser() == user"
        )
    ],
    normalizationContext: ['groups' => ['provider-read']],
)]
class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['user-read-barber', 'user-read', 'provider-read', 'service-read', 'feedback-read'])]
    protected UuidInterface|string $id;

    #[ORM\Column(length: 255)]
    #[Groups(['user-read-provider','user-create-provider', 'provider-read'])]
    #[Assert\Regex(pattern: '/^\d{10}$/')]
    private ?string $kbis = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user-read-provider','user-create-provider', 'provider-update', 'provider-read'])]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user-read-provider','user-create-provider', 'provider-update', 'provider-read'])]
    #[Assert\Regex(pattern: '/^((\\+[1-9]{1,4}[ \\-]*)|(\\([0-9]{2,3}\\)[ \\-]*)|([0-9]{2,4})[ \\-]*)*?[0-9]{3,4}?[ \\-]*[0-9]{3,4}?$/')]
    private ?string $phone = null;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Establishment::class, orphanRemoval: true)]
    private Collection $establishments;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Barber::class, orphanRemoval: true)]
    private Collection $barbers;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Feedback::class, orphanRemoval: true)]
    private Collection $feedback;

    #[ORM\OneToOne(inversedBy: 'provider', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user-read-provider','user-create-provider', 'user-read', 'provider-update', 'provider-read', 'feedback-read'])]
    #[Assert\Length(min: 2, max: 120)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user-read-provider','user-create-provider', 'provider-update', 'provider-read'])]
    #[Assert\Length(min: 5)]
    private ?string $address = null;

    #[ORM\OneToMany(mappedBy: 'provider_id', targetEntity: Appointment::class)]
    private Collection $appointments;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Service::class, orphanRemoval: true)]
    private Collection $services;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user-read-provider', 'provider-update-image', 'provider-read', 'user-read'])]
    private ?string $image = null;

    public function __construct()
    {
        $this->establishments = new ArrayCollection();
        $this->barbers = new ArrayCollection();
        $this->feedback = new ArrayCollection();
        $this->appointments = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getKbis(): ?string
    {
        return $this->kbis;
    }

    public function setKbis(string $kbis): static
    {
        $this->kbis = $kbis;

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

    /**
     * @return Collection<int, Establishment>
     */
    public function getEstablishments(): Collection
    {
        return $this->establishments;
    }

    public function addEstablishment(Establishment $establishment): static
    {
        if (!$this->establishments->contains($establishment)) {
            $this->establishments->add($establishment);
            $establishment->setProvider($this);
        }

        return $this;
    }

    public function removeEstablishment(Establishment $establishment): static
    {
        if ($this->establishments->removeElement($establishment)) {
            // set the owning side to null (unless already changed)
            if ($establishment->getProvider() === $this) {
                $establishment->setProvider(null);
            }
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
            $barber->setProvider($this);
        }

        return $this;
    }

    public function removeBarber(Barber $barber): static
    {
        if ($this->barbers->removeElement($barber)) {
            // set the owning side to null (unless already changed)
            if ($barber->getProvider() === $this) {
                $barber->setProvider(null);
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
            $feedback->setProvider($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): static
    {
        if ($this->feedback->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getProvider() === $this) {
                $feedback->setProvider(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
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
            $appointment->setProvider($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): static
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getProvider() === $this) {
                $appointment->setProvider(null);
            }
        }

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
            $service->setProvider($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getProvider() === $this) {
                $service->setProvider(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }
}

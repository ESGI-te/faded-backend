<?php

namespace App\Entity\Auth;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\UserInfoController;
use App\Entity\Appointment;
use App\Entity\Feedback;
use App\Entity\Provider;
use App\Enum\LocalesEnum;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(

    operations: [
        new GetCollection(
            normalizationContext: ['groups' => 'user-read'],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Post(
            normalizationContext: ['groups' => 'user-read'],
            denormalizationContext: ['groups' => 'user-create'],
            validationContext: ['groups' => ['user-create']],
        ),
        new Get(
            normalizationContext: ['groups' => 'user-read'],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Get(
            name: 'user-info',
            uriTemplate: '/auth/user',
            controller: UserInfoController::class,
            security: "is_granted('ROLE_USER')",
            read: false,
            normalizationContext: ['groups' => 'user-read']
        ),
        new Patch(
            normalizationContext: ['groups' => 'user-read-update'],
            denormalizationContext: ['groups' => 'user-update'],
            validationContext: ['groups' => 'user-update']
        ),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ],
)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    use Auth;
    #[Groups(['user-read', 'user-create', 'user-update', 'user-read-update'])]
    #[ORM\Column(length: 255)]
    #[Assert\Email(groups: ['user-create', 'user-update'])]
    private ?string $email = null;

    #[Groups(['user-read', 'user-create', 'user-update', 'user-read-update', 'establishment-read'])]
    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 80, groups: ['user-create', 'user-update', 'appointment-read'])]
    private ?string $lastName = null;

    #[Groups(['user-read', 'user-create', 'user-update', 'user-read-update', 'establishment-read'])]
    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 80, groups: ['user-create', 'user-update', 'appointment-read'])]
    private ?string $firstName = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Appointment::class, orphanRemoval: true)]
    private Collection $appointments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Feedback::class, orphanRemoval: true)]
    private Collection $feedback;

    #[Groups(['user-read', 'user-update', 'user-read-update'])]
    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: [LocalesEnum::FR->value, LocalesEnum::EN->value], groups: ['user-update'])]
    private ?string $locale = LocalesEnum::FR->value;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Provider $provider = null;

    public function __construct()
    {
        $this->appointments = new ArrayCollection();
        $this->feedback = new ArrayCollection();
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

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstName;
    }

    public function setFirstname(string $firstName): static
    {
        $this->firstName = $firstName;

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
            $appointment->setUser($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): static
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getUser() === $this) {
                $appointment->setUser(null);
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
            $feedback->setUser($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): static
    {
        if ($this->feedback->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getUser() === $this) {
                $feedback->setUser(null);
            }
        }

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(Provider $provider): static
    {
        // set the owning side of the relation if necessary
        if ($provider->getUser() !== $this) {
            $provider->setUser($this);
        }

        $this->provider = $provider;

        return $this;
    }
}

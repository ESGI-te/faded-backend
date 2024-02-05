<?php

namespace App\Entity\Auth;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\UserInfoController;
use App\Dto\UserDto;
use App\Entity\Appointment;
use App\Entity\Barber;
use App\Entity\Feedback;
use App\Entity\Provider;
use App\Entity\ResetPasswordToken;
use App\Enum\LocalesEnum;
use App\Repository\UserRepository;
use App\State\CreateUserProcessor;
use App\State\UpdateUserPasswordProcessor;
use App\State\UserDtoValidationProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
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
            processor: CreateUserProcessor::class,
        ),
        new Post(
            uriTemplate: '/users/barber',
            normalizationContext: ['groups' => 'user-read-barber'],
            denormalizationContext: ['groups' => 'user-create-barber'],
            validationContext: ['groups' => ['user-create']],
        ),
        new Post(
            uriTemplate: '/users/provider',
            normalizationContext: ['groups' => ['user-read-provider','user-read']],
            denormalizationContext: ['groups' => ['user-create-provider','user-create']],
            validationContext: ['groups' => ['user-create']],
        ),
        new Get(
            normalizationContext: ['groups' => 'user-read'],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Get(
            uriTemplate: '/auth/user',
            controller: UserInfoController::class,
            normalizationContext: ['groups' => 'user-read'],
            security: "is_granted('ROLE_USER')",
            read: false,
        ),
        new Patch(
            normalizationContext: ['groups' => 'user-read-update'],
            denormalizationContext: ['groups' => 'user-update'],
            security: "is_granted('ROLE_BARBER') 
            or is_granted('ROLE_PROVIDER') 
            or is_granted('ROLE_ADMIN')",
            validationContext: ['groups' => 'user-update'],
//            input: UserDto::class,
//            processor: UserDtoValidationProcessor::class
        ),
        new Patch(
            uriTemplate: '/users/{id}/password',
            normalizationContext: ['groups' => 'user-update-password-read'],
            denormalizationContext: ['groups' => 'user-update-password'],
            validationContext: ['groups' => 'user-update-password'],
            name: 'user_update_password',
            processor: UpdateUserPasswordProcessor::class,
        ),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ],
)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('email')]
#[ApiFilter(SearchFilter::class, properties: ['email' => 'exact'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    use Auth;
    #[Groups(['user-read', 'user-create', 'user-update', 'user-read-update', 'user-create-barber', 'user-read-barber', 'barber-read'])]
    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Email(groups: ['user-create', 'user-update'])]
    private ?string $email = null;

    #[Groups([
        'user-read',
        'user-create',
        'user-update',
        'user-read-update',
        'establishment-read',
        'user-create-barber',
        'user-read-barber',
        'appointment-read'
    ])]
    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 80, groups: ['user-create', 'user-update', 'appointment-read', 'user-read-barber'])]
    private ?string $lastName = null;

    #[Groups([
        'user-read',
        'user-create',
        'user-update',
        'user-read-update',
        'establishment-read',
        'user-create-barber',
        'user-read-barber',
        'appointment-read'
    ])]
    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 80, groups: ['user-create', 'user-update', 'appointment-read', 'user-read-barber'])]
    private ?string $firstName = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Appointment::class, orphanRemoval: true)]
    private Collection $appointments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Feedback::class, orphanRemoval: true)]
    private Collection $feedback;

    #[Groups(['user-read', 'user-update', 'user-read-update', 'user-read-barber'])]
    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: [LocalesEnum::FR->value, LocalesEnum::EN->value], groups: ['user-update'])]
    private ?string $locale = LocalesEnum::FR->value;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    #[Groups(['user-read','user-read-provider', 'user-create-provider'])]
    private ?Provider $provider = null;

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'])]
    #[Groups(['user-read','user-create-barber', 'user-read-barber'])]
    private ?Barber $barber = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ResetPasswordToken::class)]
    private Collection $resetPasswordTokens;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->appointments = new ArrayCollection();
        $this->feedback = new ArrayCollection();
        $this->resetPasswordTokens = new ArrayCollection();
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

    public function getBarber(): ?Barber
    {
        return $this->barber;
    }

    public function setBarber(?Barber $barber): static
    {
        $this->barber = $barber;

        return $this;
    }

    public function getResetPasswordTokens(): Collection
    {
        return $this->resetPasswordTokens;
    }
}

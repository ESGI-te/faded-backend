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
use App\Enum\LocalesEnum;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => 'user-read']),
        new Post(
            normalizationContext: ['groups' => 'user-read'],
            denormalizationContext: ['groups' => 'user-write']
        ),
        new Get(
            name: 'user-info',
            uriTemplate: '/users/me',
            controller: UserInfoController::class,
            security: "is_granted('ROLE_USER')",
            read: false,
            normalizationContext: ['groups' => 'user-read']
        ),
        new Get(normalizationContext: ['groups' => 'user-read']),
        new Patch(
            normalizationContext: ['groups' => 'user-read'],
            denormalizationContext: ['groups' => 'user-write']
        ),
        new Delete(security: "is_granted('ROLE_ADMIN')"),
    ],
)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    use Auth;
    #[Groups(['user-read', 'user-write'])]
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[Groups(['user-read', 'user-write'])]
    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[Groups(['user-read', 'user-write'])]
    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Appointment::class, orphanRemoval: true)]
    private Collection $appointments;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Feedback::class, orphanRemoval: true)]
    private Collection $feedback;

    #[ORM\Column(length: 255)]
    private ?LocalesEnum $locale = LocalesEnum::FR;

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

    public function getName(): ?string
    {
        return $this->last_name;
    }

    public function setName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->first_name;
    }

    public function setFirstname(string $first_name): static
    {
        $this->first_name = $first_name;

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
            $appointment->setUserId($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): static
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getUserId() === $this) {
                $appointment->setUserId(null);
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
            $feedback->setUserId($this);
        }

        return $this;
    }

    public function removeFeedback(Feedback $feedback): static
    {
        if ($this->feedback->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getUserId() === $this) {
                $feedback->setUserId(null);
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
}

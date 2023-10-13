<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Auth\User;
use App\Repository\BarberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: BarberRepository::class)]
#[ApiResource]
class Barber
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected UuidInterface|string $id;

    #[ORM\ManyToOne(inversedBy: 'barbers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Provider $provider = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_id = null;

    #[ORM\OneToMany(mappedBy: 'barber', targetEntity: Feedback::class)]
    private Collection $feedback;

    #[ORM\OneToMany(mappedBy: 'barber', targetEntity: Appointment::class, orphanRemoval: true)]
    private Collection $appointments;

    public function __construct()
    {
        $this->feedback = new ArrayCollection();
        $this->appointments = new ArrayCollection();
    }

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

    public function getUser(): ?User
    {
        return $this->user_id;
    }

    public function setUser(User $user_id): static
    {
        $this->user_id = $user_id;

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

}

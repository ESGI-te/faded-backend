<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\WeeklyOpeningHoursRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: WeeklyOpeningHoursRepository::class)]
#[ApiResource]
class WeeklyOpeningHours
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected UuidInterface|string $id;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?OpeningHours $monday = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?OpeningHours $tuesday = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?OpeningHours $wednesday = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?OpeningHours $thursday = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?OpeningHours $friday = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?OpeningHours $saturday = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?OpeningHours $sunday = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMonday(): ?OpeningHours
    {
        return $this->monday;
    }

    public function setMonday(?OpeningHours $monday): static
    {
        $this->monday = $monday;

        return $this;
    }

    public function getTuesday(): ?OpeningHours
    {
        return $this->tuesday;
    }

    public function setTuesday(?OpeningHours $tuesday): static
    {
        $this->tuesday = $tuesday;

        return $this;
    }

    public function getWednesday(): ?OpeningHours
    {
        return $this->wednesday;
    }

    public function setWednesday(?OpeningHours $wednesday): static
    {
        $this->wednesday = $wednesday;

        return $this;
    }

    public function getThursday(): ?OpeningHours
    {
        return $this->thursday;
    }

    public function setThursday(?OpeningHours $thursday): static
    {
        $this->thursday = $thursday;

        return $this;
    }

    public function getFriday(): ?OpeningHours
    {
        return $this->friday;
    }

    public function setFriday(?OpeningHours $friday): static
    {
        $this->friday = $friday;

        return $this;
    }

    public function getSaturday(): ?OpeningHours
    {
        return $this->saturday;
    }

    public function setSaturday(?OpeningHours $saturday): static
    {
        $this->saturday = $saturday;

        return $this;
    }

    public function getSunday(): ?OpeningHours
    {
        return $this->sunday;
    }

    public function setSunday(?OpeningHours $sunday): static
    {
        $this->sunday = $sunday;

        return $this;
    }
}

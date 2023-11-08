<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\WeeklyOpeningHoursRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WeeklyOpeningHoursRepository::class)]
#[ApiResource]
class WeeklyOpeningHours
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['establishment-read'])]
    protected UuidInterface|string $id;

    #[ORM\Column(type: "time", nullable: true)]
    #[Groups(['establishment-read'])]
    private ?\DateTimeInterface $mondayOpen = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Groups(['establishment-read'])]
    private ?\DateTimeInterface $mondayClose = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Groups(['establishment-read'])]
    private ?\DateTimeInterface $tuesdayOpen = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Groups(['establishment-read'])]
    private ?\DateTimeInterface $tuesdayClose = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Groups(['establishment-read'])]
    private ?\DateTimeInterface $wednesdayOpen = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Groups(['establishment-read'])]
    private ?\DateTimeInterface $wednesdayClose = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Groups(['establishment-read'])]
    private ?\DateTimeInterface $thursdayOpen = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Groups(['establishment-read'])]
    private ?\DateTimeInterface $thursdayClose = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Groups(['establishment-read'])]
    private ?\DateTimeInterface $fridayOpen = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Groups(['establishment-read'])]
    private ?\DateTimeInterface $fridayClose = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Groups(['establishment-read'])]
    private ?\DateTimeInterface $saturdayOpen = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Groups(['establishment-read'])]
    private ?\DateTimeInterface $saturdayClose = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Groups(['establishment-read'])]
    private ?\DateTimeInterface $sundayOpen = null;

    #[ORM\Column(type: "time", nullable: true)]
    #[Groups(['establishment-read'])]
    private ?\DateTimeInterface $sundayClose = null;

    public function getId(): UuidInterface|string
    {
        return $this->id;
    }

    public function getMondayOpen(): ?\DateTimeInterface
    {
        return $this->mondayOpen;
    }

    public function setMondayOpen(?\DateTimeInterface $mondayOpen): void
    {
        $this->mondayOpen = $mondayOpen;
    }

    public function getMondayClose(): ?\DateTimeInterface
    {
        return $this->mondayClose;
    }

    public function setMondayClose(?\DateTimeInterface $mondayClose): void
    {
        $this->mondayClose = $mondayClose;
    }

    public function getTuesdayOpen(): ?\DateTimeInterface
    {
        return $this->tuesdayOpen;
    }

    public function setTuesdayOpen(?\DateTimeInterface $tuesdayOpen): void
    {
        $this->tuesdayOpen = $tuesdayOpen;
    }

    public function getTuesdayClose(): ?\DateTimeInterface
    {
        return $this->tuesdayClose;
    }

    public function setTuesdayClose(?\DateTimeInterface $tuesdayClose): void
    {
        $this->tuesdayClose = $tuesdayClose;
    }

    public function getWednesdayOpen(): ?\DateTimeInterface
    {
        return $this->wednesdayOpen;
    }

    public function setWednesdayOpen(?\DateTimeInterface $wednesdayOpen): void
    {
        $this->wednesdayOpen = $wednesdayOpen;
    }

    public function getWednesdayClose(): ?\DateTimeInterface
    {
        return $this->wednesdayClose;
    }

    public function setWednesdayClose(?\DateTimeInterface $wednesdayClose): void
    {
        $this->wednesdayClose = $wednesdayClose;
    }

    public function getThursdayOpen(): ?\DateTimeInterface
    {
        return $this->thursdayOpen;
    }

    public function setThursdayOpen(?\DateTimeInterface $thursdayOpen): void
    {
        $this->thursdayOpen = $thursdayOpen;
    }

    public function getThursdayClose(): ?\DateTimeInterface
    {
        return $this->thursdayClose;
    }

    public function setThursdayClose(?\DateTimeInterface $thursdayClose): void
    {
        $this->thursdayClose = $thursdayClose;
    }

    public function getFridayOpen(): ?\DateTimeInterface
    {
        return $this->fridayOpen;
    }

    public function setFridayOpen(?\DateTimeInterface $fridayOpen): void
    {
        $this->fridayOpen = $fridayOpen;
    }

    public function getFridayClose(): ?\DateTimeInterface
    {
        return $this->fridayClose;
    }

    public function setFridayClose(?\DateTimeInterface $fridayClose): void
    {
        $this->fridayClose = $fridayClose;
    }

    public function getSaturdayOpen(): ?\DateTimeInterface
    {
        return $this->saturdayOpen;
    }

    public function setSaturdayOpen(?\DateTimeInterface $saturdayOpen): void
    {
        $this->saturdayOpen = $saturdayOpen;
    }

    public function getSaturdayClose(): ?\DateTimeInterface
    {
        return $this->saturdayClose;
    }

    public function setSaturdayClose(?\DateTimeInterface $saturdayClose): void
    {
        $this->saturdayClose = $saturdayClose;
    }

    public function getSundayOpen(): ?\DateTimeInterface
    {
        return $this->sundayOpen;
    }

    public function setSundayOpen(?\DateTimeInterface $sundayOpen): void
    {
        $this->sundayOpen = $sundayOpen;
    }

    public function getSundayClose(): ?\DateTimeInterface
    {
        return $this->sundayClose;
    }

    public function setSundayClose(?\DateTimeInterface $sundayClose): void
    {
        $this->sundayClose = $sundayClose;
    }

}

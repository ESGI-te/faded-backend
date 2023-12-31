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
use ApiPlatform\Metadata\Put;
use App\Controller\AcceptProviderController;
use App\Controller\UploadBarberImageController;
use App\Repository\ProviderRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProviderRequestRepository::class)]
#[ApiResource(
    operations: [
    new Post(
        denormalizationContext: ['groups' => ['provider-request-write']],
    ),
    new Get(),
    new GetCollection(),
    new Patch(
        uriTemplate: '/provider-requests/{id}/accept',
        controller: AcceptProviderController::class,
        denormalizationContext: ['groups' => ['provider-request-patch']],
    ),
    new Put(),
    new Delete()
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['token' => 'exact'])]
class ProviderRequest
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected UuidInterface|string $id;

    #[ORM\Column(length: 255)]
    #[Groups(['provider-request-write'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['provider-request-write'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['provider-request-write'])]
    private ?string $personalEmail = null;

    #[ORM\Column(length: 255)]
    #[Groups(['provider-request-write'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[Groups(['provider-request-write'])]
    private ?string $professionalEmail = null;

    #[ORM\Column(length: 255)]
    #[Groups(['provider-request-write'])]
    private ?string $companyName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['provider-request-write'])]
    private ?string $companyAddress = null;

    #[Groups(['provider-request-write'])]
    #[ORM\Column(length: 255)]
    private ?string $kbis = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $tokenExpirationDate = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

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

    public function getPersonalEmail(): ?string
    {
        return $this->personalEmail;
    }

    public function setPersonalEmail(string $personalEmail): static
    {
        $this->personalEmail = $personalEmail;

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

    public function getProfessionalEmail(): ?string
    {
        return $this->professionalEmail;
    }

    public function setProfessionalEmail(string $professionalEmail): static
    {
        $this->professionalEmail = $professionalEmail;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getCompanyAddress(): ?string
    {
        return $this->companyAddress;
    }

    public function setCompanyAddress(string $companyAddress): static
    {
        $this->companyAddress = $companyAddress;

        return $this;
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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getTokenExpirationDate(): ?\DateTimeInterface
    {
        return $this->tokenExpirationDate;
    }

    public function setTokenExpirationDate(?\DateTimeInterface $tokenExpirationDate): static
    {
        $this->tokenExpirationDate = $tokenExpirationDate;

        return $this;
    }
}

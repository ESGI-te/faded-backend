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
use App\Enum\ProviderRequestStatusEnum;
use App\Repository\ProviderRequestRepository;
use App\State\ProviderRequestProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProviderRequestRepository::class)]
#[ApiResource(
    operations: [
    new Post(
        denormalizationContext: ['groups' => ['provider-request-write']],
    ),
    new GetCollection(
        security: "is_granted('ROLE_ADMIN')"
    ),
    new Patch(
        denormalizationContext: ['groups' => ['provider-request-update']],
        security: "is_granted('ROLE_ADMIN')",
        processor: ProviderRequestProcessor::class
    ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['companyName' => 'ipartial'])]
class ProviderRequest
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected UuidInterface|string $id;

    #[ORM\Column(length: 255)]
    #[Groups(['provider-request-write'])]
    #[Assert\Length(min: 2, max: 80)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['provider-request-write'])]
    #[Assert\Length(min: 2, max: 80)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['provider-request-write'])]
    #[Assert\Email]
    private ?string $personalEmail = null;

    #[ORM\Column(length: 255)]
    #[Groups(['provider-request-write'])]
    #[Assert\Regex(pattern: '/^((\\+[1-9]{1,4}[ \\-]*)|(\\([0-9]{2,3}\\)[ \\-]*)|([0-9]{2,4})[ \\-]*)*?[0-9]{3,4}?[ \\-]*[0-9]{3,4}?$/')]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[Groups(['provider-request-write'])]
    #[Assert\Email]
    private ?string $professionalEmail = null;

    #[ORM\Column(length: 255)]
    #[Groups(['provider-request-write'])]
    #[Assert\Length(min: 2, max: 120)]
    private ?string $companyName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['provider-request-write'])]
    #[Assert\Length(min: 5)]
    private ?string $companyAddress = null;

    #[Groups(['provider-request-write'])]
    #[ORM\Column(length: 255)]
    #[Assert\Regex(pattern: '/^\d{9}$/')]
    private ?string $kbis = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(type: \DateTimeInterface::class)]
    private ?\DateTimeImmutable $createdAt = null;
    #[Groups(['provider-request-update'])]
    #[ORM\Column(length: 50)]
    #[Assert\Choice(callback: [ProviderRequestStatusEnum::class, 'getValues'])]
    private ?string $status = ProviderRequestStatusEnum::PENDING->value;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

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

    public function getTokenExpirationDate(): ?\DateTimeInterface
    {
        return $this->tokenExpirationDate;
    }

    public function setTokenExpirationDate(?\DateTimeInterface $tokenExpirationDate): static
    {
        $this->tokenExpirationDate = $tokenExpirationDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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
}

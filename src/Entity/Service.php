<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\ServiceRepository;
use App\State\AddProviderProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => 'service-read'],
        ),
        new Post(
            normalizationContext: ['groups' => 'service-read'],
            denormalizationContext: ['groups' => 'service-create'],
            security: 'is_granted("ROLE_PROVIDER")',
            processor: AddProviderProcessor::class,
        ),
        new Patch(
            normalizationContext: ['groups' => 'service-read'],
            denormalizationContext: ['groups' => 'service-update'],
            security: 'is_granted("ROLE_PROVIDER") and object.getProvider().getUser() == user'
        ),
        new Delete(
            normalizationContext: ['groups' => 'service-delete'],
            security: 'is_granted("ROLE_PROVIDER") and object.getProvider().getUser() == user
            or is_granted("ROLE_ADMIN")'
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'establishment' => 'exact',
])]
class Service
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['establishment-read', 'appointment-read', 'service-read', 'feedback-read'])]
    protected UuidInterface|string $id;

    #[ORM\Column(length: 255)]
    #[Groups(['establishment-read', 'appointment-read', 'service-read', 'service-create', 'service-update', 'feedback-read'])]
    #[Assert\Length(min: 2)]
    private ?string $name = null;

    #[ORM\Column(type: Types::FLOAT)]
    #[Groups(['establishment-read', 'appointment-read', 'service-read', 'service-create', 'service-update'])]
    #[Assert\Type(type: 'float')]
    #[Assert\PositiveOrZero]
    private ?float $price = null;

    #[ORM\Column]
    #[Groups(['establishment-read', 'appointment-read', 'service-read', 'service-create', 'service-update'])]
    #[Assert\Type(type: 'integer')]
    #[Assert\Positive]
    private ?int $duration = null;
    #[Groups(['service-read', 'service-create', 'service-update'])]
    #[ORM\JoinColumn(nullable: true,onDelete: "CASCADE")]
    #[ORM\ManyToMany(targetEntity: Establishment::class, inversedBy: 'services')]
    private Collection $establishment;

    #[ORM\ManyToOne(inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['establishment-read', 'service-read', 'service-create', 'service-update'])]
    private ?ServiceCategory $category = null;

    #[ORM\ManyToOne(inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['service-create'])]
    private ?Provider $provider = null;

    public function __construct()
    {
        $this->establishment = new ArrayCollection();
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection<int, Establishment>
     */
    public function getEstablishment(): Collection
    {
        return $this->establishment;
    }

    public function addEstablishment(Establishment $establishment): static
    {
        if (!$this->establishment->contains($establishment)) {
            $this->establishment->add($establishment);
        }

        return $this;
    }

    public function removeEstablishment(Establishment $establishment): static
    {
        $this->establishment->removeElement($establishment);

        return $this;
    }

    public function getCategory(): ?ServiceCategory
    {
        return $this->category;
    }

    public function setCategory(?ServiceCategory $category): static
    {
        $this->category = $category;

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
}

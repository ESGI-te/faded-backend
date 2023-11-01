<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ServiceCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: ServiceCategoryRepository::class)]
#[ApiResource]
class ServiceCategory
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected UuidInterface|string $id;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Service::class, orphanRemoval: true)]
    private Collection $services;

    #[ORM\ManyToMany(targetEntity: Establishment::class, inversedBy: 'serviceCategories')]
    private Collection $establishement;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->establishement = new ArrayCollection();
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
            $service->setCategory($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getCategory() === $this) {
                $service->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Establishment>
     */
    public function getEstablishement(): Collection
    {
        return $this->establishement;
    }

    public function addEstablishement(Establishment $establishement): static
    {
        if (!$this->establishement->contains($establishement)) {
            $this->establishement->add($establishement);
        }

        return $this;
    }

    public function removeEstablishement(Establishment $establishement): static
    {
        $this->establishement->removeElement($establishement);

        return $this;
    }

}

<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ImageRepository;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/images',
            normalizationContext: ['groups' => ['image-read']],
            denormalizationContext: ['groups' => ['image-write']]
        ),
        new Delete()
    ]
)]
class Image
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['image-read'])]
    private UuidInterface|string $id;

    #[Groups(['establishment-image-read', 'barber-image-read','image-read', 'image-write' ])]
    #[ORM\Column(length: 255)]
    #[Assert\Url]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[Groups(['image-read', 'image-write'])]
    private ?Establishment $establishment = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getEstablishment(): ?Establishment
    {
        return $this->establishment;
    }

    public function setEstablishment(?Establishment $establishment): static
    {
        $this->establishment = $establishment;

        return $this;
    }
}

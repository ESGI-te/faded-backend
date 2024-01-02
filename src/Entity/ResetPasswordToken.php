<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\Auth\User;
use App\Repository\ResetPasswordTokenRepository;
use App\State\ResetPasswordTokenProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
   operations: [
       new Post(uriTemplate: '/reset_password'),
       new GetCollection()
    ],
    normalizationContext: ['groups' => ['password-reset-token-read']],
    denormalizationContext: ['groups' => ['password-reset-token-write']],
    processor: ResetPasswordTokenProcessor::class
)]
#[ApiFilter(SearchFilter::class, properties: ['token' => 'exact'])]
#[ORM\Entity(repositoryClass: ResetPasswordTokenRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ResetPasswordToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $expiresAt = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'resetPasswordTokens')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['password-reset-token-read'])]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email()]
    #[Groups(['password-reset-token-write'])]
    private ?string $email = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }
    #[ORM\PrePersist]
    public function setToken(): static
    {
        $this->token = bin2hex(random_bytes(32));

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }
    #[ORM\PrePersist]
    public function setExpiresAt(): static
    {
        $this->expiresAt = new \DateTime('+24 hours');

        return $this;
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
}

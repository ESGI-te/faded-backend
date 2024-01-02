<?php

namespace App\Entity\Auth;

use ApiPlatform\Metadata\ApiProperty;
use App\Enum\RolesEnum;
use App\Enum\UserAccountTypeEnum;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait Auth
{
    #[Groups([
        'user-read',
        'appointment-read',
        'user-create-barber',
        'user-read-barber',
        'barber-read',
        'password-reset-token-read',
    ])]
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected UuidInterface|string $id;

    #[Groups(['user-read', 'user-create-barber', 'user-read-barber', 'user-create-provider'])]
    #[ORM\Column]
    private array $roles = [RolesEnum::USER->value];

    #[Groups(['user-hidden'])]
    #[ORM\Column]
    private string $password = '';

    #[Groups(['user-create', 'user-create-barber', 'user-update-password'])]
    #[Assert\Length(min: 6, groups: ['user-create', 'user-create-barber', 'user-update-password'])]
    private ?string $plainPassword = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    /** @see UserInterface */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /** @see UserInterface */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    #[Groups(['user:write'])]
    #[ApiProperty(example: 'normal')]
    public function setAccountType(string $type): void
    {
        $enum = UserAccountTypeEnum::from($type);

        if ($enum === UserAccountTypeEnum::ADMIN) {
            $this->setRoles([RolesEnum::ADMIN->value]);

            return;
        }

        $this->setRoles([RolesEnum::USER->value]);
    }

    /** @see PasswordAuthenticatedUserInterface */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /** @see UserInterface */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }
}

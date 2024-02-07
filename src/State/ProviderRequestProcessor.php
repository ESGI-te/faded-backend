<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Auth\User;
use App\Entity\Provider;
use App\Enum\ProviderRequestStatusEnum;
use App\Enum\RolesEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Faker\Factory;

class ProviderRequestProcessor implements ProcessorInterface
{

    private EntityManagerInterface $entityManager;
    private $faker;
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->faker = Factory::create();
        $this->entityManager = $entityManager;
    }


    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data->getStatus() === ProviderRequestStatusEnum::REJECTED->value) {
            $this->entityManager->persist($data);
            $this->entityManager->flush();
            return;
        }

        if($data->getStatus() !== ProviderRequestStatusEnum::APPROVED->value) return;

        $user = new User();
        $user->setFirstName($data->getFirstName());
        $user->setLastName($data->getLastName());
        $user->setEmail($data->getPersonalEmail());
        $user->setPassword($this->faker->password);
        $user->setRoles([RolesEnum::USER->value, RolesEnum::PROVIDER->value]);

        $provider = new Provider();
        $provider->setUser($user);
        $provider->setName($data->getCompanyName());
        $provider->setAddress($data->getCompanyAddress());
        $provider->setEmail($data->getProfessionalEmail());
        $provider->setPhone($data->getPhone());
        $provider->setKbis($data->getKbis());

        $this->entityManager->persist($user);
        $this->entityManager->persist($provider);
        $this->entityManager->persist($data);
        $this->entityManager->flush();

    }

}
<?php

namespace App\DataFixtures;

use App\Entity\Auth\User;
use App\Enum\RolesEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private  \Faker\Generator $faker;
    public const USER_REFERENCE = 'user';
    public const USER_PROVIDER_REFERENCE = 'user-provider';
    public const USER_ADMIN_REFERENCE = 'user-Admin';

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        for($i = 0; $i < 10; $i++) {
            $user = $this->createUser();
            $this->addReference(self::USER_REFERENCE . '-' . $i, $user);
            $manager->persist($user);
        }

        for($i = 0; $i < 10; $i++) {
            $provider_user = $this->createUser();
            $provider_user->setRoles([RolesEnum::PROVIDER->value]);
            $this->addReference(self::USER_PROVIDER_REFERENCE . '-' . $i, $provider_user);
            $manager->persist($provider_user);
        }

        $admin_user = $this->createUser();
        $admin_user->setRoles([RolesEnum::ADMIN->value]);
        $this->addReference(self::USER_ADMIN_REFERENCE, $admin_user);
        $manager->persist($admin_user);

        $manager->flush();
    }

    private function createUser():User {
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($user, "password");
        $user->setEmail($this->faker->email);
        $user->setLastName($this->faker->lastName);
        $user->setFirstName($this->faker->firstName);
        $user->setPassword($hashedPassword);
        return $user;
    }
}
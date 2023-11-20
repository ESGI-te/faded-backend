<?php

namespace App\DataFixtures;

use App\Entity\Auth\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Yaml\Yaml;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    public const USER_REFERENCE = 'user_';
    public const USER_PROVIDER_REFERENCE = 'user_provider_';
    public const USER_ADMIN_REFERENCE = 'user_admin_';

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        $users = Yaml::parseFile(__DIR__ . '/data/users.yaml');
        $createReferenceCallback = $this->createReference();

        foreach ($users as $userData) {
            $user = new User();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setEmail($userData['email']);
            $user->setLastName($userData['lastName']);
            $user->setFirstName($userData['firstName']);
            $user->setRoles($userData['roles']);
            $user->setPassword($hashedPassword);
            $user->setlocale($userData['locale']);
            $manager->persist($user);

            $createReferenceCallback($user);
        }

        $manager->flush();
    }

    private function createReference(): \Closure
    {
        $userIndex = 1;
        $providerIndex = 1;
        $adminIndex = 1;

        return function (User $user) use (&$userIndex, &$providerIndex, &$adminIndex) {
            if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                $this->addReference(self::USER_ADMIN_REFERENCE . $adminIndex, $user);
                $adminIndex++;
                return;
            }
            if (in_array('ROLE_PROVIDER', $user->getRoles(), true)) {
                $this->addReference(self::USER_PROVIDER_REFERENCE . $providerIndex, $user);
                $providerIndex++;
                return;
            }
            $this->addReference(self::USER_REFERENCE . $userIndex, $user);
            $userIndex++;
            return;
        };
    }
}

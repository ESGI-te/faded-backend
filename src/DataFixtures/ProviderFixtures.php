<?php

namespace App\DataFixtures;

use App\Entity\Provider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProviderFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROVIDER_REFERENCE = 'provider';

    public function load(ObjectManager $manager):void
    {
        $faker = Factory::create();
        
        for ($i = 0; $i < 10; $i++) {
            $provider = new Provider();
            $provider->setEmail($faker->email);
            $provider->setPhone($faker->phoneNumber);
            $provider->setKbis('/providers/kbis/' . $faker->slug . '-kbis.pdf');
            $provider->setUser($this->getReference(UserFixtures::USER_PROVIDER_REFERENCE . '-' . $i));
            $manager->persist($provider);
            $this->addReference(self::PROVIDER_REFERENCE . '-' . $i, $provider);
        }
        $manager->flush();
    }

    public function getDependencies(): array
{
    return [
        UserFixtures::class,
    ];
}

}
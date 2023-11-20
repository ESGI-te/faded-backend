<?php

namespace App\DataFixtures;

use App\Entity\Provider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Yaml\Yaml;

class ProviderFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROVIDER_REFERENCE = 'provider_';

    public function load(ObjectManager $manager): void
    {
        $providers = Yaml::parseFile(__DIR__ . '/data/providers.yaml');
        $index = 1;

        foreach ($providers as $providerData) {
            $provider = new Provider();
            $provider->setName($providerData['name']);
            $provider->setEmail($providerData['email']);
            $provider->setPhone($providerData['phone']);
            $provider->setAddress($providerData['address']);
            $provider->setKbis($providerData['kbis']);
            $provider->setUser($this->getReference(UserFixtures::USER_PROVIDER_REFERENCE . $index));
            $manager->persist($provider);
            $this->addReference(self::PROVIDER_REFERENCE . $index, $provider);
            $index++;
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

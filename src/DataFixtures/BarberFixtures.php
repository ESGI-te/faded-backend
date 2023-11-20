<?php

namespace App\DataFixtures;

use App\Entity\Barber;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BarberFixtures extends Fixture implements DependentFixtureInterface
{

    public const BARBER_REFERENCE = 'barber_reference';
    private const PLANNING = [
        'monday' => [
            'open' => '2000-01-01 09:00:00',
            'close' => '2000-01-01 19:00:00',
        ],
        'tuesday' => [
            'open' => '2000-01-01 09:00:00',
            'close' => '2000-01-01 19:00:00',
        ],
        'wednesday' => [
            'open' => '2000-01-01 09:00:00',
            'close' => '2000-01-01 19:00:00',
        ],
        'thursday' => [
            'open' => '2000-01-01 09:00:00',
            'close' => '2000-01-01 19:00:00',
        ],
        'friday' => [
            'open' => '2000-01-01 09:00:00',
            'close' => '2000-01-01 19:00:00',
        ],
        'saturday' => [
            'open' => '2000-01-01 09:00:00',
            'close' => '2000-01-01 19:00:00',
        ],
        'sunday' => [],
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $barber = new Barber();
            $barber->setFirstName($faker->firstName);
            $barber->setLastName($faker->lastName);
            $barber->setPlanning(self::PLANNING);
            $barber->setProvider($this->getReference(ProviderFixtures::PROVIDER_REFERENCE . '-' . $i));
            $this->addReference(self::BARBER_REFERENCE . '-' . $i, $barber);
            $manager->persist($barber);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            ProviderFixtures::class,
        ];
    }
}

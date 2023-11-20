<?php

namespace App\DataFixtures;

use App\Entity\Establishment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EstablishmentFixtures extends Fixture implements DependentFixtureInterface
{

    public const ESTABLISHEMENT_REFERENCE = 'establishsment_reference';
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
            $establishment = new Establishment();
            $establishment->setName($faker->streetName);
            $establishment->setPhone($faker->phoneNumber);
            $establishment->setEmail($faker->email);
            $establishment->setAddress($faker->address);
            $establishment->setLatitude($faker->latitude);
            $establishment->setLongitude($faker->longitude);
            $establishment->setPlanning(self::PLANNING);
            $establishment->setProvider($this->getReference(ProviderFixtures::PROVIDER_REFERENCE . '-' . $i));
            $establishment->addBarber($this->getReference(BarberFixtures::BARBER_REFERENCE . '-' . $i));
            $this->setReference(self::ESTABLISHEMENT_REFERENCE . '-' . $i, $establishment);
            $manager->persist($establishment);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProviderFixtures::class,
            BarberFixtures::class,
        ];
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Establishment;
use App\Entity\Feedback;
use App\Repository\ProviderRepository;
use App\Repository\WeeklyOpeningHoursRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EstablishmentFixtures extends Fixture implements DependentFixtureInterface
{

    public const ESTABLISHEMENT_REFERENCE = 'establishsment_reference';

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
            $establishment->setOpeningHours($this->getReference(WeeklyOpeningHoursFixtures::WEEKLY_OPENING_HOURS_REFERENCE . '-' . $i));
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
            WeeklyOpeningHoursFixtures::class,
            BarberFixtures::class,
        ];
    }
}

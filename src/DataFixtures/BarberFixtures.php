<?php

namespace App\DataFixtures;

use App\Entity\Barber;
use App\Entity\Establishment;
use App\Repository\ProviderRepository;
use App\Repository\WeeklyOpeningHoursRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BarberFixtures extends Fixture implements DependentFixtureInterface
{

    public const BARBER_REFERENCE = 'barber_reference';

    public function load(ObjectManager $manager):void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $barber = new Barber();
            $barber->setFirstName($faker->firstName);
            $barber->setLastName($faker->lastName);
            $barber->setWorkingHours($this->getReference(WeeklyOpeningHoursFixtures::WEEKLY_OPENING_HOURS_BARBER_REFERENCE. '-' . $i));
            $barber->setProvider($this->getReference(ProviderFixtures::PROVIDER_REFERENCE . '-' . $i));
            $this->addReference(self::BARBER_REFERENCE.'-'.$i,$barber);
            $manager->persist($barber);
            $manager->flush();
        }

    }

    public function getDependencies(): array
    {
        return [
            ProviderFixtures::class,
            WeeklyOpeningHoursFixtures::class,
        ];
    }
}
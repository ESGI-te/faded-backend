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

class EstablishementFixtures extends Fixture implements DependentFixtureInterface
{

    public const ESTABLISHEMENT_REFERENCE = 'establishsment_reference';

    public function load(ObjectManager $manager):void
    {
       $faker = Factory::create();
        
        for ($i = 0; $i < 10; $i++) {
            $establishement = new Establishment();
            $establishement->setName($faker->streetName);
            $establishement->setPhone($faker->phoneNumber);
            $establishement->setEmail($faker->email);
            $establishement->setAddress($faker->address);
            $establishement->setLatitude($faker->latitude);
            $establishement->setLongitude($faker->longitude);
            $establishement->setOpeningHours($this->getReference(WeeklyOpeningHoursFixtures::WEEKLY_OPENING_HOURS_REFERENCE.'-'.$i));
            $establishement->setProvider($this->getReference(ProviderFixtures::PROVIDER_REFERENCE . '-' . $i));
            $establishement->addBarber($this->getReference(BarberFixtures::BARBER_REFERENCE.'-'.$i));
            $this->setReference(self::ESTABLISHEMENT_REFERENCE.'-'. $i, $establishement);
            $manager->persist($establishement);
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
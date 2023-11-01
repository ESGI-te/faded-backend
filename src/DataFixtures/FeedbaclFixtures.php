<?php

namespace App\DataFixtures;

use App\Entity\Feedback;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FeedbaclFixtures extends Fixture implements DependentFixtureInterface
{
    public const FEEDBACK_REFERENCE = 'feedback_reference';

    public function load(ObjectManager $manager):void
    {
        $faker = Factory::create();

         for($i = 0; $i < 10; $i++ )
            {
                $feedback = new Feedback();
                $feedback->setNote(rand(0, 5));
                $feedback->setComment($faker->text(200));
                $feedback->setDateTime($faker->dateTime);
                $feedback->setProvider($this->getReference(ProviderFixtures::PROVIDER_REFERENCE . '-' . $i));
                $feedback->setUser($this->getReference(UserFixtures::USER_REFERENCE . '-' . $i));
                $feedback->setEstablishment($this->getReference(EstablishementFixtures::ESTABLISHEMENT_REFERENCE.'-'.$i));
                $feedback->setBarber($this->getReference(BarberFixtures::BARBER_REFERENCE.'-'.$i));
                $this->setProvidedService($i, $feedback);
                $this->addReference(self::FEEDBACK_REFERENCE.'-'.$i,$feedback);
                $manager->persist($feedback);
            }
            $manager->flush();

    }

    private function setProvidedService(int $index,Feedback $feedback): void {
        if($index == 0 || $index == 9)
        {
            $feedback->setService($this->getReference(ServiceFixtures::HAIR_SERVICE_REFERENCE. '-' . rand(0,2)));
        }
        else if($index == 2 || $index == 8)
        {
            $feedback->setService($this->getReference(ServiceFixtures::BEARD_SERVICE_REFERENCE. '-' . rand(0,2)));
        }
        else if($index == 2 || $index == 7)
        {
            $feedback->setService($this->getReference(ServiceFixtures::COLOR_SERVICE_REFERENCE. '-' . rand(0,2)));
        }
        else if($index == 4 || $index == 6)
        {
            $feedback->setService($this->getReference(ServiceFixtures::FACE_SERVICE_REFERENCE. '-' . rand(0,2)));
        }
        else
        {
            $feedback->setService($this->getReference(ServiceFixtures::PERMANENT_SERVICE_REFERENCE. '-'. rand(0,3)));
        }
    }

    public function getDependencies():array
    {
        return [
            ProviderFixtures::class,
            UserFixtures::class,
            ServiceFixtures::class,
        ];
    }
}
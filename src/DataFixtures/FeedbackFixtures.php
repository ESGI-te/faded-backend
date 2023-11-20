<?php

namespace App\DataFixtures;

use App\Entity\Feedback;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Yaml\Yaml;

class FeedbaclFixtures extends Fixture implements DependentFixtureInterface
{
    public const FEEDBACK_REFERENCE = 'feedback_reference';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $establishments = Yaml::parseFile(__DIR__ . '/data/establishments.yaml');
        $index = 0;

        foreach ($establishments as $establishmentData) {

            for ($i = 0; $i < random_int(0, 30); $i++) {
                $establishment = $this->getReference(EstablishmentFixtures::ESTABLISHEMENT_REFERENCE . $index);
                $user = $this->getReference(UserFixtures::USER_REFERENCE . $i);
                $barbers = $establishment->getBarbers();
                $barber = $barbers[random_int(0, count($barbers))];
                $provider = $establishment->getProvider();
                $services = $establishment->getServices();

                $feedback = new Feedback();
                $feedback->setComment($faker->text(200));
                $feedback->setNote($faker->numberBetween(0, 5));
                $feedback->setDateTime($faker->dateTimeBetween('-1 years', 'now'));
                $feedback->setService($services[random_int(0, count($services))]);

                $provider->addFeedback($feedback);
                $establishment->addFeedback($feedback);
                $user->addFeedback($feedback);
                $barber->addFeedback($feedback);

                $manager->persist($feedback, $barber, $user, $establishment, $provider);
            }

            $index++;
        }

        $manager->flush();
    }


    public function getDependencies(): array
    {
        return [
            ProviderFixtures::class,
            UserFixtures::class,
            ServiceFixtures::class,
            EstablishmentFixtures::class,
            BarberFixtures::class,
        ];
    }
}

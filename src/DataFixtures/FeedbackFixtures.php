<?php

namespace App\DataFixtures;

use App\Entity\Feedback;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Yaml\Yaml;

class FeedbackFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $establishments = Yaml::parseFile(__DIR__ . '/data/establishments.yaml');
        $index = 1;

        foreach ($establishments as $establishmentData) {

            for ($i = 1; $i < random_int(0, 30); $i++) {
                $establishment = $this->getReference(EstablishmentFixtures::ESTABLISHEMENT_REFERENCE . $index);
                $user = $this->getReference(UserFixtures::USER_REFERENCE . $i);
                $barbers = $establishment->getBarbers();
                $barber = $barbers[random_int(0, count($barbers) - 1)];
                $provider = $establishment->getProvider();
                $services = $establishment->getServices();
                $service = $services[random_int(0, count($services) - 1)];

                $feedback = new Feedback();
                $feedback->setComment($faker->text(200));
                $feedback->setNote($faker->numberBetween(0, 5));
                $feedback->setDateTime($faker->dateTimeBetween('-1 years', 'now'));
                $feedback->setService($service);

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
            ServiceCategoryFixtures::class,
            EstablishmentFixtures::class,
        ];
    }
}

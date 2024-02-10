<?php

namespace App\DataFixtures;

use App\Entity\Auth\User;
use App\Entity\Barber;
use App\Entity\Establishment;
use App\Entity\Provider;
use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Yaml\Yaml;

class EstablishmentFixtures extends Fixture implements DependentFixtureInterface
{

    public const ESTABLISHEMENT_REFERENCE = 'establishsment_';
    private const PLANNING = [
        'monday' => [
            'open' => '09:00:00',
            'close' => '18:00:00',
            'IsOpen' => true,
        ],
        'tuesday' => [
            'open' => '09:00:00',
            'close' => '18:00:00',
            'IsOpen' => true,
        ],
        'wednesday' => [
            'open' => '09:00:00',
            'close' => '18:00:00',
            'IsOpen' => true,
        ],
        'thursday' => [
            'open' => '09:00:00',
            'close' => '18:00:00',
            'IsOpen' => true,
        ],
        'friday' => [
            'open' => '09:00:00',
            'close' => '18:00:00',
            'IsOpen' => true,
        ],
        'saturday' => [
            'open' => '09:00:00',
            'close' => '18:00:00',
            'IsOpen' => true,
        ],
        'sunday' => [
            'open' => '09:00:00',
            'close' => '18:00:00',
            'IsOpen' => false,
        ],
    ];
    private $faker;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->faker = Factory::create();
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $establishments = Yaml::parseFile(__DIR__ . '/data/establishments.yaml');
        $services = Yaml::parseFile(__DIR__ . '/data/services.yaml');
        $index = 1;

        foreach ($establishments as $establishmentData) {

            $establishment = new Establishment();
            $establishment->setName($establishmentData['name']);
            $establishment->setPhone($establishmentData['phone']);
            $establishment->setEmail($establishmentData['email']);
            $establishment->setAddress($establishmentData['address']);
            $establishment->setLatitude($establishmentData['latitude']);
            $establishment->setLongitude($establishmentData['longitude']);
            $establishment->setProvider($this->getReference($establishmentData['provider']));
            $establishment->setPlanning(self::PLANNING);
            $establishment->setStatus($establishmentData['status']);

            $provider = $establishment->getProvider();

            for ($i = 0; $i < random_int(1, 12); $i++) {
                $user = $this->createBarberUser();
                $barber = $this->createBarber($user);
                $manager->persist($user);
                $manager->persist($barber);
                $establishment->addBarber($barber);
                $provider->addBarber($barber);
            }

            for ($i = 1; $i < random_int(2, 10); $i++) {
                $category = $this->getReference(ServiceCategoryFixtures::CATEGORY_REFERENCE . $i);
                $establishment->addServiceCategory($category);

                $categoryServices = array_filter($services, function ($services) use ($i) {
                    return  $services['category'] === ServiceCategoryFixtures::CATEGORY_REFERENCE . $i;
                });

                foreach ($categoryServices as $serviceData) {
                    $service = $this->createService($serviceData, $establishment->getProvider());
                    $category->addService($service);
                    $manager->persist($service, $category);
                    $establishment->addService($service);
                }
            }

            $manager->persist($establishment, $provider);
            $this->addReference(self::ESTABLISHEMENT_REFERENCE . $index, $establishment);
            $index++;
        }

        $manager->flush();
    }

    private function createBarberUser (): User {
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');

        $user->setFirstName($this->faker->firstName);
        $user->setLastName($this->faker->lastName);
        $user->setEmail($this->faker->email);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER', 'ROLE_BARBER']);

        return $user;
    }

    private function createBarber(User $user): Barber
    {
        $barber = new Barber();

        $barber->setUser($user);
        $barber->setFirstName($user->getFirstname());
        $barber->setLastName($user->getLastname());
        $barber->setEmail($user->getEmail());
        $barber->setPlanning(self::PLANNING);

        return $barber;
    }

    private function createService($data, Provider $provider): Service
    {
        $service = new Service();
        $service->setName($data['name']);
        $service->setPrice($data['price']);
        $service->setDuration($data['duration']);
        $service->setProvider($provider);
        return $service;
    }

    public function getDependencies(): array
    {
        return [
            ProviderFixtures::class,
            ServiceCategoryFixtures::class,
        ];
    }
}

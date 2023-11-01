<?php

namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ServiceFixtures extends Fixture implements DependentFixtureInterface
{
    public const HAIR_SERVICE_REFERENCE = 'hair_service_reference';
    public const BEARD_SERVICE_REFERENCE = 'beard_service_reference';
    public const COLOR_SERVICE_REFERENCE = 'color_service_reference';
    public const FACE_SERVICE_REFERENCE = 'face_service_reference';
    public const PERMANENT_SERVICE_REFERENCE = 'permanent_service_reference';
    private array $hair_services =
    array(
        array('name' => 'Coupe Ciseaux & Coiffage', 'price' => 35.00, 'duration' => 60),
        array('name' => 'Coupe & Coiffage + Shampoing', 'price' => 30.00, 'duration' => 30),
        array('name' => 'Coupe & Coiffage + moustache', 'price' => 40.00, 'duration' => 60)
    );

    private array $beard_services =
    array(
        array('name' => 'Coupe & barbe', 'price' => 35.00, 'duration' => 60),
        array('name' => 'Tail de barbe', 'price' => 30.00, 'duration' => 30),
        array('name' => 'Barbe + Contour à la cire', 'price' => 30.00, 'duration' => 30)
    );

    private array $color_services =
    array(
        array('name' => 'Decoloration + Coloration - court', 'price' => 75.00, 'duration' => 120),
        array('name' => 'Decoloration + Coloration - moyen', 'price' => 115.00, 'duration' => 120),
        array('name' => 'Decoloration + Coloration - long', 'price' => 225.00, 'duration' => 120)
    );

    private array $face_services =
    array(
        array('name' => 'Soin de visage classique', 'price' => 30.00, 'duration' => 30),
        array('name' => 'Soin de visage premium', 'price' => 60.00, 'duration' => 60),
        array('name' => 'Soin capilaire traitant', 'price' => 25.00, 'duration' => 30)
    );

    private array $permanent_services =
    array(
        array('name' => 'Permanante (curly) - court', 'price' => 60.00, 'duration' => 90),
        array('name' => 'Permanante (curly) - long', 'price' => 90.00, 'duration' => 90),
        array('name' => 'Défrisage - court', 'price' => 60.00, 'duration' => 60),
        array('name' => 'Défrisage - long', 'price' => 100.00, 'duration' => 90)
    );

    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i < sizeof($this->hair_services); $i++) {
            $service = new Service();
            $service->setName($this->hair_services[$i]['name']);
            $service->setPrice($this->hair_services[$i]['price']);
            $service->setDuration($this->hair_services[$i]['duration']);
            $service->setCategory($this->getReference(ServiceCategoryFixtures::CATEGORY_REFERENCE_1));
            $establishment1 = $this->getReference(EstablishmentFixtures::ESTABLISHEMENT_REFERENCE . '-0');
            $establishment = $this->getReference(EstablishmentFixtures::ESTABLISHEMENT_REFERENCE . '-9');
            $service->addEstablishment($establishment1);
            $service->addEstablishment($establishment);
            $this->addReference(self::HAIR_SERVICE_REFERENCE . '-' . $i, $service);
            $manager->persist($service);
        }

        for ($i = 0; $i < sizeof($this->beard_services); $i++) {
            $service = new Service();
            $service->setName($this->beard_services[$i]['name']);
            $service->setPrice($this->beard_services[$i]['price']);
            $service->setDuration($this->beard_services[$i]['duration']);
            $service->setCategory($this->getReference(ServiceCategoryFixtures::CATEGORY_REFERENCE_2));
            $establishment1 = $this->getReference(EstablishmentFixtures::ESTABLISHEMENT_REFERENCE . '-1');
            $establishment = $this->getReference(EstablishmentFixtures::ESTABLISHEMENT_REFERENCE . '-8');
            $service->addEstablishment($establishment1);
            $service->addEstablishment($establishment);
            $this->addReference(self::BEARD_SERVICE_REFERENCE . '-' . $i, $service);
            $manager->persist($service);
        }

        for ($i = 0; $i < sizeof($this->color_services); $i++) {
            $service = new Service();
            $service->setName($this->color_services[$i]['name']);
            $service->setPrice($this->color_services[$i]['price']);
            $service->setDuration($this->color_services[$i]['duration']);
            $service->setCategory($this->getReference(ServiceCategoryFixtures::CATEGORY_REFERENCE_3));
            $establishment1 = $this->getReference(EstablishmentFixtures::ESTABLISHEMENT_REFERENCE . '-3');
            $establishment = $this->getReference(EstablishmentFixtures::ESTABLISHEMENT_REFERENCE . '-7');
            $service->addEstablishment($establishment1);
            $service->addEstablishment($establishment);
            $this->addReference(self::COLOR_SERVICE_REFERENCE . '-' . $i, $service);
            $manager->persist($service);
        }

        for ($i = 0; $i < sizeof($this->face_services); $i++) {
            $service = new Service();
            $service->setName($this->face_services[$i]['name']);
            $service->setPrice($this->face_services[$i]['price']);
            $service->setDuration($this->face_services[$i]['duration']);
            $service->setCategory($this->getReference(ServiceCategoryFixtures::CATEGORY_REFERENCE_4));
            $establishment1 = $this->getReference(EstablishmentFixtures::ESTABLISHEMENT_REFERENCE . '-4');
            $establishment = $this->getReference(EstablishmentFixtures::ESTABLISHEMENT_REFERENCE . '-6');
            $service->addEstablishment($establishment1);
            $service->addEstablishment($establishment);
            $this->addReference(self::FACE_SERVICE_REFERENCE . '-' . $i, $service);
            $manager->persist($service);
        }

        for ($i = 0; $i < sizeof($this->permanent_services); $i++) {
            $service = new Service();
            $service->setName($this->permanent_services[$i]['name']);
            $service->setPrice($this->permanent_services[$i]['price']);
            $service->setDuration($this->permanent_services[$i]['duration']);
            $service->setCategory($this->getReference(ServiceCategoryFixtures::CATEGORY_REFERENCE_5));
            $establishment = $this->getReference(EstablishmentFixtures::ESTABLISHEMENT_REFERENCE . '-5');
            $service->addEstablishment($establishment);
            $this->addReference(self::PERMANENT_SERVICE_REFERENCE . '-' . $i, $service);
            $manager->persist($service);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ServiceCategoryFixtures::class, EstablishmentFixtures::class];
    }
}

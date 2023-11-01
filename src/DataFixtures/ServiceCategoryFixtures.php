<?php

namespace App\DataFixtures;

use App\Entity\ServiceCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ServiceCategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public const  CATEGORY_REFERENCE_1 = 'Coupe de cheveux + Coiffage';
    public const  CATEGORY_REFERENCE_2 = 'Coupe & coiffage + Barbe';
    public const  CATEGORY_REFERENCE_3 = 'Coloration & Décoloration';
    public const  CATEGORY_REFERENCE_4 = 'Soins - Visage / Cheveux';
    public const  CATEGORY_REFERENCE_5 = 'Lissage & Permanente';


    public function load(ObjectManager $manager):void
    {
        $max = 5;
        for ($i=0 ; $i<$max ; $i++ )
        {
            $category = new ServiceCategory();
            $referenceName = 'CATEGORY_REFERENCE_' . $i+1;
            $dynamicReference = constant("self::$referenceName");
            $category->setName($dynamicReference);
            $establishement1 = $this->getReference(EstablishementFixtures::ESTABLISHEMENT_REFERENCE.'-'.$i);
            $establishement = $this->getReference(EstablishementFixtures::ESTABLISHEMENT_REFERENCE.'-'.$max*2-1-$i);
            $category->addEstablishement($establishement1);
            $category->addEstablishement($establishement);
            $this->addReference($dynamicReference,$category);
            $manager->persist($category);
        }

        $manager->flush();
    }


    public function getDependencies():array
    {
        return [EstablishementFixtures::class];
    }
}
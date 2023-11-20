<?php

namespace App\DataFixtures;

use App\Entity\ServiceCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class ServiceCategoryFixtures extends Fixture
{
    public const  CATEGORY_REFERENCE = 'category_';

    public function load(ObjectManager $manager): void
    {
        $categories = Yaml::parseFile(__DIR__ . '/data/serviceCategories.yaml');
        $index = 1;

        foreach ($categories as $categoryData) {
            $category = new ServiceCategory();
            $category->setName($categoryData['name']);
            $manager->persist($category);
            $this->addReference(self::CATEGORY_REFERENCE . $index, $category);
            $index++;
        }

        $manager->flush();
    }
}

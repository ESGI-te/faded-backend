<?php

namespace App\DataFixtures;

use App\Entity\WeeklyOpeningHours;
use App\Repository\OpeningHoursRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class WeeklyOpeningHoursFixtures extends Fixture
{

    public const WEEKLY_OPENING_HOURS_REFERENCE = 'weekly-opening-hours';
    public const WEEKLY_OPENING_HOURS_BARBER_REFERENCE = 'weekly-opening-hours-barbers';

    public function load(ObjectManager $manager):void
    {
        $open_hour = new \DateTime("09:00");
        $close_hour = new \DateTime("20:00");
        $close_hour_barber = new \DateTime("18:00");

        for($i = 0; $i < 10; $i++) {
            $week = new WeeklyOpeningHours();
        
            $this->setOpeningHours($week, $open_hour, $close_hour);

            $manager->persist($week);

    
            $this->addReference(self::WEEKLY_OPENING_HOURS_REFERENCE . '-' . $i, $week);
        }

        for($i = 0; $i < 10; $i++) {
            $week_barber = new WeeklyOpeningHours();
            $this->setOpeningHoursBarber($week, $open_hour, $close_hour_barber);
            $manager->persist($week_barber);
            $this->addReference(self::WEEKLY_OPENING_HOURS_BARBER_REFERENCE . '-'. $i, $week_barber);
        }

        $manager->flush();
    }

    private function setOpeningHours(WeeklyOpeningHours $week, \DateTime $open_hour, \DateTime $close_hour): void {
        $week->setMondayOpen($open_hour);
        $week->setMondayClose($close_hour);

        $week->setTuesdayOpen($open_hour);
        $week->setTuesdayClose($close_hour);

        $week->setWednesdayOpen($open_hour);
        $week->setWednesdayClose($close_hour);

        $week->setThursdayOpen($open_hour);
        $week->setThursdayClose($close_hour);

        $week->setFridayOpen($open_hour);
        $week->setFridayClose($close_hour);

        $week->setSaturdayOpen($open_hour);
        $week->setSaturdayClose($close_hour);
    }

    private function setOpeningHoursBarber(WeeklyOpeningHours $week, \DateTime $open_hour, \DateTime $close_hour): void {
        $week->setMondayOpen($open_hour);
        $week->setMondayClose($close_hour);

        $week->setTuesdayOpen($open_hour);
        $week->setTuesdayClose($close_hour);

        $week->setThursdayOpen($open_hour);
        $week->setThursdayClose($close_hour);

        $week->setFridayOpen($open_hour);
        $week->setFridayClose($close_hour);

    }
}
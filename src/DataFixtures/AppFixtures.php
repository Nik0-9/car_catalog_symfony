<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Car;
use App\Enum\CarStatus;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $car = new Car();
            $car->setBrand("Brand " . $i);
            $car->setModel("Model " . $i);
            $car->setProductionYear(2000 + $i);
            $car->setPrice(100 + $i);
            $car->setStatus(CarStatus::AVAILABLE);
            $manager->persist($car);
        }

        $manager->flush();
    }
}

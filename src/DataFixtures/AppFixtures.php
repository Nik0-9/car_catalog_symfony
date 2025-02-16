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
        for ($i = 0; $i < 100; $i++) {
            $car = new Car();
            $car->setBrand("Brand " . $i);
            $car->setModel("Model " . $i);
            $car->setProductionYear(random_int(1900, date('Y')));
            $car->setPrice(random_int(1000, 10000));
            $car->setStatus(CarStatus::AVAILABLE);
            $manager->persist($car);
        }

        $manager->flush();
    }
}

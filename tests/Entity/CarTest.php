<?php

namespace App\Tests\Entity;

use App\Entity\Car;
use App\Enum\CarStatus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CarTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidCar(): void
    {
        $car = new Car();
        $car->setBrand('Toyota');
        $car->setModel('Corolla');
        $car->setProductionYear(2020);
        $car->setPrice('25000.00');
        $car->setStatus(CarStatus::AVAILABLE);

        $violations = $this->validator->validate($car);
        $this->assertCount(0, $violations, "Expected no validation errors");
    }

    public function testInvalidBrand(): void
    {
        $car = new Car();
        $car->setBrand('T'); 
        $car->setModel('Corolla');
        $car->setProductionYear(2020);
        $car->setPrice('25000.00');
        $car->setStatus(CarStatus::AVAILABLE);

        $violations = $this->validator->validate($car);
        $this->assertGreaterThan(0, count($violations), "Expected at least one validation error");
        
        $errorMessages = array_map(fn($violation) => $violation->getMessage(), iterator_to_array($violations));
        $this->assertContains('Brand must be at least 2 characters', $errorMessages);
    }

    public function testInvalidModel(): void
    {
        $car = new Car();
        $car->setBrand('Toyota');
        $car->setModel('C'); 
        $car->setProductionYear(2020);
        $car->setPrice('25000.00');
        $car->setStatus(CarStatus::AVAILABLE);

        $violations = $this->validator->validate($car);
        $this->assertGreaterThan(0, count($violations), "Expected at least one validation error");
        
        $errorMessages = array_map(fn($violation) => $violation->getMessage(), iterator_to_array($violations));
        $this->assertContains('Model must be at least 2 characters', $errorMessages);
    }

    public function testInvalidProductionYear(): void
    {
        $car = new Car();
        $car->setBrand('Toyota');
        $car->setModel('Corolla');
        $car->setProductionYear(2026); 
        $car->setPrice('25000.00');
        $car->setStatus(CarStatus::AVAILABLE);

        $violations = $this->validator->validate($car);
        $this->assertGreaterThan(0, count($violations), "Expected at least one validation error");
        
        $errorMessages = array_map(fn($violation) => $violation->getMessage(), iterator_to_array($violations));
        $this->assertContains('Production year must be between 1900 and the current year', $errorMessages);
    }

    public function testInvalidPrice(): void
    {
        $car = new Car();
        $car->setBrand('Toyota');
        $car->setModel('Corolla');
        $car->setProductionYear(2020);
        $car->setPrice('-500'); 
        $car->setStatus(CarStatus::AVAILABLE);

        $violations = $this->validator->validate($car);
        $this->assertGreaterThan(0, count($violations), "Expected at least one validation error");
        
        $errorMessages = array_map(fn($violation) => $violation->getMessage(), iterator_to_array($violations));
        $this->assertContains('Price must be higher than 0', $errorMessages);
    }
}
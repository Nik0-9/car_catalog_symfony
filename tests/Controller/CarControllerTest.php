<?php
namespace App\Tests\Controller;

use App\Entity\Car;
use App\Enum\CarStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CarControllerTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager;
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
    }
    

    public function testIndex()
    {
        $this->client->request('GET', '/api/cars');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
    }

    public function testShowExistingCar()
    {
        $car = new Car();
        $car->setBrand('Toyota');
        $car->setModel('Corolla');
        $car->setPrice(price: 20000);
        $car->setProductionYear(2020);
        $car->setStatus(CarStatus::AVAILABLE);

        $this->entityManager->persist($car);
        $this->entityManager->flush();

        $this->client->request('GET', '/api/car/' . $car->getId());

        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Toyota', $data['brand']);
        $this->assertEquals('Corolla', $data['model']);
        $this->assertEquals(20000, $data['price']);
        $this->assertEquals(2020, $data['production_year']);
        $this->assertEquals('available', $data['status']);

    }

    public function testShowNonExistingCar()
    {
        $this->client->request('GET', '/api/car/9999');
        $this->assertResponseStatusCodeSame(404);
    }

    public function testShowDeletedCar()
    {
        $car = new Car();
        $car->setBrand('Ford');
        $car->setModel('Focus');
        $car->setPrice(18000);
        $car->setProductionYear(2018);
        $car->setStatus(CarStatus::AVAILABLE);
        $car->setDeletedAt(new \DateTime()); // Simuliamo soft delete

        $this->entityManager->persist($car);
        $this->entityManager->flush();

        $this->client->request('GET', '/api/car/' . $car->getId());
        $this->assertResponseStatusCodeSame(200);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}

<?php
namespace App\Tests\Controller;

use App\Entity\Car;
use App\Enum\CarStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

final class CarControllerTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager;
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        // Pulisci il database prima di ogni test
        $this->truncateEntities([Car::class]);
    }

    private function truncateEntities(array $entities)
    {
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        
        foreach ($entities as $entity) {
            $query = $platform->getTruncateTableSQL(
                $this->entityManager->getClassMetadata($entity)->getTableName()
            );
            $connection->executeStatement($query);
        }
    }

    public function testGetCars(): void
    {
        $this->client->request('GET', '/api/cars');
        
        $this->assertEquals(JsonResponse::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function testCreateCar()
    {
        $car = [
            'brand' => 'Tesla',
            'model' => 'Model 3',
            'price' => 45000,
            'production_year' => 2023
        ];

        $this->client->request(
            'POST',
            '/api/car',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'PHP_AUTH_USER' => 'user',
                'PHP_AUTH_PW' => 'password'
            ],
            json_encode($car)
        );

        $this->assertResponseStatusCodeSame(201);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $response);
        $this->assertEquals($car['brand'], $response['brand']);
    }

    public function testCreateCarWithInvalidData()
    {
        $invalidData = [
            'brand' => '', 
            'price' => 45000
        ];

        $this->client->request(
            'POST',
            '/api/car',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'PHP_AUTH_USER' => 'user',
                'PHP_AUTH_PW' => 'password'
            ],
            json_encode($invalidData)
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testUpdateCar()
    {
        $car = $this->createTestCar();

        $updateData = [
            'brand' => 'Updated Brand',
            'model' => 'Updated Model',
            'price' => 25000,
            'production_year' => 2022
        ];

        $this->client->request(
            'PUT',
            '/api/car/' . $car->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'PHP_AUTH_USER' => 'user',
                'PHP_AUTH_PW' => 'password'
            ],
            json_encode($updateData)
        );

        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Updated Brand', $response['brand']);
    }

    public function testPatchCar()
    {
        $car = $this->createTestCar();

        $patchData = [
            'price' => 26000
        ];

        $this->client->request(
            'PATCH',
            '/api/car/' . $car->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'PHP_AUTH_USER' => 'user',
                'PHP_AUTH_PW' => 'password'
            ],
            json_encode($patchData)
        );

        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals(26000, $response['price']);
    }

    public function testSoftDeleteCar()
    {
        $car = $this->createTestCar();

        $this->client->request('DELETE', '/api/car/' . $car->getId());
        
        $this->assertResponseIsSuccessful();
        
        // Verifica che l'auto sia ancora nel database ma con deleted_at impostato
        $deletedCar = $this->entityManager->getRepository(Car::class)->find($car->getId());
        $this->assertNotNull($deletedCar->getDeletedAt());
    }

    private function createTestCar(): Car
    {
        $car = new Car();
        $car->setBrand('Test Brand');
        $car->setModel('Test Model');
        $car->setPrice(20000);
        $car->setProductionYear(2020);
        $car->setStatus(CarStatus::AVAILABLE);

        $this->entityManager->persist($car);
        $this->entityManager->flush();

        return $car;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        if ($this->entityManager) {
            $this->truncateEntities([Car::class]);
            $this->entityManager->close();
            $this->entityManager = null;
        }
    }
}
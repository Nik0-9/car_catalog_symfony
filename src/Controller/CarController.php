<?php

namespace App\Controller;

use App\Entity\Car;
use App\Enum\CarStatus;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
final class CarController extends AbstractController
{
    #[Route('/cars', name: 'car_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $cars = $entityManager
            ->getRepository(Car::class)
            ->findAll();

        $data = [];
        foreach ($cars as $car) {
            $data[] = [
                'id' => $car->getId(),
                'brand' => $car->getBrand(),
                'model' => $car->getModel(),
                'price' => $car->getPrice(),
                'status' => $car->getStatus(),
                'productionYear' => $car->getProductionYear()
            ];
        }

        return new JsonResponse($data);
    }
    #[Route('/cars', name: 'car_create', methods: ['POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['brand']) || !isset($data['model']) || !isset($data['price']) || !isset($data['productionYear'])) {
            return $this->json([
                'error' => 'Missing parameters'
            ], 400);
        }
        $car = new Car();
        $car->setBrand($data['brand']);
        $car->setModel($data['model']);
        $car->setPrice($data['price']);
        $car->setProductionYear($data['productionYear']);
        $car->setStatus(CarStatus::AVAILABLE);

        $entityManager->persist($car);
        $entityManager->flush();

        $data = [
            'id' => $car->getId(),
            'brand' => $car->getBrand(),
            'model' => $car->getModel(),
            'price' => $car->getPrice(),
            'status' => $car->getStatus(),
            'productionYear' => $car->getProductionYear()
        ];

        return new JsonResponse($data);
    }
}

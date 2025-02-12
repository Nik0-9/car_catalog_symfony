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
                'productionYear' => $car->getProductionYear(),
                'deletedAt' => $car->getDeletedAt()
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

    #[Route('/cars/{id}', name: 'project_show', methods:['GET'])]
    public function show(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $car = $entityManager->getRepository(Car::class)->find($id);

        if (!$car) {
            return $this->json([
                'error' => 'Car not found'
            ], 404);
        }
        $data =[
            'id' => $car->getId(),
            'brand' => $car->getBrand(),
            'model' => $car->getModel(),
            'price' => $car->getPrice(),
            'status' => $car->getStatus(),
            'productionYear' => $car->getProductionYear(),
            'deletedAt' => $car->getDeletedAt()
        ];
        return new JsonResponse($data);
    }

    #[Route('/cars/{id}', name: 'car_update', methods: ['PUT', 'PATCH'])]
    public function update(EntityManagerInterface $entityManager, int $id, Request $request): JsonResponse
    {
        $car = $entityManager->getRepository(Car::class)->find($id);

        if (!$car) {
            return $this->json([
                'error' => 'Car not found'
            ], 404);
        }
        //gestione richiesta PUT
        if($request->getMethod()==='PUT'){
            $data = json_decode($request->getContent(), true);
            $car->setBrand($data['brand']);
            $car->setModel($data['model']);
            $car->setPrice($data['price']);
            $car->setProductionYear($data['productionYear']);
            $car->setStatus(CarStatus::AVAILABLE);
            //gestione richiesta PATCH
        } else if($request->getMethod() === 'PATCH'){
            $data = json_decode($request->getContent(), true);
            if (isset($data['brand'])) {
                $car->setBrand($data['brand']);
            }
            if (isset($data['model'])) {
                $car->setModel($data['model']);
            }
            if (isset($data['price'])) {
                $car->setPrice($data['price']);
            }
            if (isset($data['productionYear'])) {
                $car->setProductionYear($data['productionYear']);
            }
            if (isset($data['status'])) {
                $status = CarStatus::tryFrom($data['status']);
                if ($status) {
                    $car->setStatus($status);
                } else {
                    return $this->json(['error' => 'Invalid status value'], 400);
                }
            }
        }
        $entityManager->persist($car);
        $entityManager->flush();

        return $this->json([
            'id'=>$car->getId(),
            'brand'=>$car->getBrand(),
            'model'=>$car->getModel(),
            'price'=>$car->getPrice(),
            'status' => $car->getStatus()->value
        ]);
    }

    #[Route('/cars/{id}', name: 'car_soft_delete', methods: ['DELETE'])]
    public function softDelete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $car = $entityManager->getRepository(Car::class)->find($id);

        if (!$car) {
            return $this->json([
                'error' => 'Car not found'
            ], 404);
        }

        $car->setDeletedAt(new \DateTime());
        $entityManager->persist($car);
        $entityManager->flush();

        return $this->json([
            'message' => 'Car deleted successfully'
        ]);
    }
}

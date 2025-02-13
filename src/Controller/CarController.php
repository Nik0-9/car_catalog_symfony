<?php

namespace App\Controller;

use App\Entity\Car;
use App\Enum\CarStatus;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api_')]
final class CarController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {
    }
    //Show all cars not deleted
    #[Route('/cars', name: 'car_index', methods: ['GET'])]
    public function index(): JsonResponse
    {

        $cars = $this->entityManager
            ->getRepository(Car::class)
            ->createQueryBuilder('c')
            ->andWhere('c.deleted_at IS NULL')
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($cars as $car) {
            $data[] = [
                'id' => $car->getId(),
                'brand' => $car->getBrand(),
                'model' => $car->getModel(),
                'price' => $car->getPrice(),
                'status' => $car->getStatus()->value,
                'production_year' => $car->getProductionYear(),
            ];
        }
        return new JsonResponse($data);
    }

    //shows a car seeked by id and not deleted
    #[Route('/car/{id}', name: 'project_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $car = $this->entityManager->getRepository(Car::class)->find($id);

        if (!$car) {
            throw new NotFoundHttpException('Car not found');
        }
        if ($car->getDeletedAt() !== null) {
            throw new NotFoundHttpException('Car is deleted');
        }
        return new JsonResponse([
            'id' => $car->getId(),
            'brand' => $car->getBrand(),
            'model' => $car->getModel(),
            'price' => $car->getPrice(),
            'status' => $car->getStatus(),
            'production_year' => $car->getProductionYear(),
        ]);
    }

    //create a new car
    #[Route('/car', name: 'car_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException('Invalid JSON format');
        }

        if (!isset($data['brand']) || !isset($data['model']) || !isset($data['price']) || !isset($data['production_year'])) {
            throw new BadRequestHttpException('Missing required parameters');
        }

        $car = new Car();
        $car->setBrand($data['brand']);
        $car->setModel($data['model']);
        $car->setPrice($data['price']);
        $car->setProductionYear($data['production_year']);
        $car->setStatus(CarStatus::AVAILABLE);

        $violations = $this->validator->validate($car);
        if (count($violations) > 0) {
            throw new ValidationFailedException($car, $violations);
        }

        $this->entityManager->persist($car);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $car->getId(),
            'brand' => $car->getBrand(),
            'model' => $car->getModel(),
            'price' => $car->getPrice(),
            'status' => $car->getStatus()->value,
            'production_year' => $car->getProductionYear()
        ], 201);
    }
    //update existing car seeked by id also deleted
    #[Route('/car/{id}', name: 'car_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $car = $this->entityManager->getRepository(Car::class)->find($id);
        if (!$car) {
            throw new NotFoundHttpException('Car not found');
        }

        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException('Invalid JSON format');
        }

        if ($request->getMethod() === 'PUT') {
            if (!isset($data['brand']) || !isset($data['model']) || !isset($data['price']) || !isset($data['production_year'])) {
                throw new BadRequestHttpException('Missing required parameters for PUT request');
            }
            
            $car->setBrand($data['brand']);
            $car->setModel($data['model']);
            $car->setPrice($data['price']);
            $car->setProductionYear($data['production_year']);
        } else {
            if (isset($data['brand'])) {
                $car->setBrand($data['brand']);
            }
            if (isset($data['model'])) {
                $car->setModel($data['model']);
            }
            if (isset($data['price'])) {
                $car->setPrice($data['price']);
            }
            if (isset($data['production_year'])) {
                $car->setProductionYear($data['production_year']);
            }
            if (isset($data['status'])) {
                $status = CarStatus::tryFrom($data['status']);
                if (!$status) {
                    throw new BadRequestHttpException('Invalid status value');
                }
                $car->setStatus($status);
            }
        }

        $violations = $this->validator->validate($car);
        if (count($violations) > 0) {
            throw new ValidationFailedException($car, $violations);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $car->getId(),
            'brand' => $car->getBrand(),
            'model' => $car->getModel(),
            'price' => $car->getPrice(),
            'status' => $car->getStatus()->value,
            'production_year' => $car->getProductionYear()
        ]);
    }


    #[Route('/car/{id}', name: 'car_soft_delete', methods: ['DELETE'])]
    public function softDelete(int $id): JsonResponse
    {
        $car = $this->entityManager->getRepository(Car::class)->find($id);

        if (!$car) {
            throw new NotFoundHttpException('Car not found');
        }

        if ($car->getDeletedAt() !== null) {
            throw new BadRequestHttpException('Car already deleted');
        }

        $car->setDeletedAt(new \DateTime());
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Car deleted successfully'
        ]);
    }
}
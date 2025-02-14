<?php

namespace App\Tests\EventListener;

use App\EventListener\ExceptionListener;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\ORM\Query\QueryException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ExceptionListenerTest extends TestCase
{
    private ExceptionListener $listener;
    private HttpKernelInterface $kernel;
    private \Throwable $exception;

    protected function setUp(): void
    {
        $this->listener = new ExceptionListener();
        $this->kernel = $this->createMock(HttpKernelInterface::class);
    }

    private function createEvent(\Throwable $exception): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->kernel,
            $this->createMock(\Symfony\Component\HttpFoundation\Request::class),
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );
    }

    public function testNotFoundException(): void
    {
        $exception = new NotFoundHttpException('Resource not found');
        $event = $this->createEvent($exception);
        $this->listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testMethodNotAllowedException(): void
    {
        $exception = new MethodNotAllowedHttpException([], 'Method not allowed');
        $event = $this->createEvent($exception);
        $this->listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $response->getStatusCode());
    }

    public function testBadRequestException(): void
    {
        $exception = new BadRequestHttpException('Bad request');
        $event = $this->createEvent($exception);
        $this->listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testValidationFailedException(): void
    {
        $violations = new ConstraintViolationList([
            new ConstraintViolation('Invalid value', null, [], null, 'brand', 'invalid_brand')
        ]);

        $exception = new ValidationFailedException(new \stdClass(), $violations);
        $event = $this->createEvent($exception);
        $this->listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertStringContainsString('Invalid value', $response->getContent());
        $this->assertStringContainsString('"brand"', $response->getContent());
    }

    public function testConnectionException(): void
    {
        $exception = new \Doctrine\DBAL\ConnectionException('Database connection error');
        $event = $this->createEvent($exception);
        $this->listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testQueryException(): void
    {
        $exception = new QueryException('Invalid query');
        $event = $this->createEvent($exception);
        $this->listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testGenericException(): void
    {
        $exception = new \Exception('Something went wrong');
        $event = $this->createEvent($exception);
        $this->listener->onKernelException($event);

        $response = $event->getResponse();
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}
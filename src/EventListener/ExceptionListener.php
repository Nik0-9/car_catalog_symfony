<?php

namespace App\EventListener;

use Doctrine\ORM\Query\QueryException;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Exception\ConnectionException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        //dd($exception instanceof ConnectionException);
        $statusCode = $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'An error occurred while processing your request.';
        $details = null;

        switch (true) {
            // 404
            case $exception instanceof NotFoundHttpException:
                $statusCode = Response::HTTP_NOT_FOUND;
                $message = $exception->getMessage() ? : 'Resource not found.';
                break;
            // 405
            case $exception instanceof MethodNotAllowedHttpException:
                $statusCode = Response::HTTP_METHOD_NOT_ALLOWED;
                $message = $exception->getMessage() ? : 'Method not allowed.';
                break;
            // 400
            case $exception instanceof BadRequestHttpException:
                $statusCode = Response::HTTP_BAD_REQUEST;
                $message = $exception->getMessage() ? : 'Bad request.';
                break;
            // 422
            case $exception instanceof ValidationFailedException:
                $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
                $message = $exception->getMessage() ? : 'Validation failed.';
                $details = $this->formatValidationErrors($exception);
                break;
            // 503
            case $exception instanceof ConnectionException:
                $statusCode = Response::HTTP_SERVICE_UNAVAILABLE;
                $message = $exception->getMessage() ? : 'Database connection error';
                break;
            // 400
            case $exception instanceof QueryException:
                $statusCode = Response::HTTP_BAD_REQUEST;
                $message = $exception->getMessage() ? : 'Invalid query';
                break;
            // 400
            case $exception instanceof \JsonException:
                $statusCode = Response::HTTP_BAD_REQUEST;
                $message = $exception->getMessage() ? : 'Invalid JSON format';
                break;
            // 500
            // case $exception instanceof DBALException:
            //     $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            //     $message = $exception->getMessage() ? : 'Internal server error.';
            //     break;
        }
        $data = [
            'status' => 'error',
            'message' => $message,
            'code' => $statusCode,
        ];

        if ($details !== null) {
            $data['details'] = $details;
        }

        $response = new JsonResponse($data, $statusCode);
        $event->setResponse($response);
    }

    private function formatValidationErrors(ValidationFailedException $exception): array
    {
        $errors = [];
        $violations = $exception->getViolations();
        foreach ($violations as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }
        return $errors;
    }
}
<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'An error occurred while processing your request.';
        $details = null;

        switch (true) {
            case $exception instanceof NotFoundHttpException:
                $statusCode = Response::HTTP_NOT_FOUND;
                $message = $exception->getMessage() ?: 'Resource not found.';
                break;

            case $exception instanceof MethodNotAllowedHttpException:
                $statusCode = Response::HTTP_METHOD_NOT_ALLOWED;
                $message = $exception->getMessage() ?: 'Method not allowed.';
                break;

            case $exception instanceof BadRequestHttpException:
                $statusCode = Response::HTTP_BAD_REQUEST;
                $message = $exception->getMessage() ?: 'Bad request.';
                break;

            case $exception instanceof DBALException:
                $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                $message = $exception->getMessage() ?: 'Database error.';

            case $exception instanceof ValidationFailedException:
                $statusCode = Response::HTTP_BAD_REQUEST;
                $message = $exception->getMessage() ?: 'Validation failed.';
                $details = $this->formatValidationErrors($exception);
                break;

            case $exception instanceof QueryException:
                $statusCode = Response::HTTP_BAD_REQUEST;
                $message = 'Invalid query syntax';
                break;

            case $exception instanceof \JsonException:
                $statusCode = Response::HTTP_BAD_REQUEST;
                $message = 'Invalid JSON format';
                break;
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
<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/* This subscriber is used to standardize the error codes */
class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onException',
        ];
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        // If the exception is HandlerFailedException, rethrow the previous exception,
        // this exception is triggered by Messenger component
        if ($exception instanceof HandlerFailedException) {
            throw $exception->getPrevious();
        }

        // Mapping different exception types to specific HTTP status codes
        switch (true) {
            case $exception instanceof UnauthorizedHttpException: $httpCode = Response::HTTP_UNAUTHORIZED;break;
            case $exception instanceof AccessDeniedException: $httpCode = Response::HTTP_FORBIDDEN;break;
            case $exception instanceof \DomainException: $httpCode = Response::HTTP_BAD_REQUEST;break;
            case $exception instanceof ResourceNotFoundException: $httpCode = Response::HTTP_NOT_FOUND;break;
        }

        // Handling ValidationFailedException separately to provide detailed validation errors
        if ($exception instanceof ValidationFailedException) {
            $errors = [];

            foreach ($exception->getViolations() as $violation) {
                $parameters = [];

                foreach ($violation->getParameters() as $key => $parameter) {
                    $parameters[trim($key, '{} ')] = trim($parameter, '\\"');
                }

                $errors[] = [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                    'code' => $violation->getCode(),
                    'parameters' => $parameters
                ];
            }

            // Set the response with validation errors and a 400 Bad Request status
            $event->setResponse(new JsonResponse([
                'errors' => $errors,
                'message' => $exception->getMessage(),
                'code' => $exception->getCode()
            ], Response::HTTP_BAD_REQUEST));

            return;
        }

        // Set a generic error response with the determined HTTP status code
        $event->setResponse(new JsonResponse([
            'message' => $exception->getMessage(),
            'code' => $exception->getCode()
        ], $httpCode));
    }
}
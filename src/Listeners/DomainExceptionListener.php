<?php

namespace App\Listeners;

use App\Exception\AbstractDomainException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class DomainExceptionListener implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof AbstractDomainException) {
            return;
        }

        $response = new JsonResponse(
            [
                'code' => $exception->getDomainError(),
                'message' => $exception->getMessage(),
            ],
            $exception->getHttpStatusCode()
        );

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}

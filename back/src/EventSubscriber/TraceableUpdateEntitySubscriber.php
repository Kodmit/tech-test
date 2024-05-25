<?php

namespace App\EventSubscriber;

use App\Entity\Trace;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use App\Attribute\Traceable;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsDoctrineListener(event: Events::preUpdate, priority: 500, connection: 'default')]
readonly class TraceableUpdateEntitySubscriber
{
    public function __construct(
        private TokenStorageInterface  $tokenStorage,
        private TraceableFlushSubscriber $traceableFlushSubscriber
    ) {}

    #[NoReturn] public function preUpdate(PreUpdateEventArgs $args): void
    {
        $token = $this->tokenStorage->getToken();

        if (null === $token) {
            return;
        }

        $reflectionClass = new \ReflectionClass($args->getObject());
        $attributes = $reflectionClass->getAttributes(Traceable::class);

        if ([] === $attributes) {
            return;
        }

        $traceableAttribute = $attributes[0]->newInstance();

        if (Traceable::MODE_CREATE === $traceableAttribute->getWatchMode()) {
            return;
        }

        $objectManager = $args->getObjectManager();

        $className = get_class($args->getObject());
        $watchedProperties = $traceableAttribute->getProperties();

        foreach ($watchedProperties as $watchedProperty) {
            if (false === $args->hasChangedField($watchedProperty)) {
                continue;
            }

            $trace = new Trace(
                $token->getUser(),
                Trace::ACTION_UPDATE,
                $className,
                $watchedProperty,
                $args->getOldValue($watchedProperty),
                $args->getNewValue($watchedProperty)
            );

            $objectManager->persist($trace);
        }

        $this->traceableFlushSubscriber->onPreUpdate();
    }
}
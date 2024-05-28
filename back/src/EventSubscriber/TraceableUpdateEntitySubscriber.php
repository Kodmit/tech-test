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
        // Get the logged user
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            return;
        }

        // Check if the Traceable attribute is called in the entity, if not, we leave
        $reflectionClass = new \ReflectionClass($args->getObject());
        $attributes = $reflectionClass->getAttributes(Traceable::class);
        if ([] === $attributes) {
            return;
        }

        // Create an instance of the attribute and check if is in update mode, if not we leave
        $traceableAttribute = $attributes[0]->newInstance();
        if (Traceable::MODE_CREATE === $traceableAttribute->getWatchMode()) {
            return;
        }

        $objectManager = $args->getObjectManager();
        $className = get_class($args->getObject());
        $watchedProperties = $traceableAttribute->getProperties();

        foreach ($watchedProperties as $watchedProperty) {
            // Check if the created property is in the watched properties list, if not continue
            if (false === $args->hasChangedField($watchedProperty)) {
                continue;
            }

            // Create and persist the trace with the data
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

        // Use this method in order to avoid infinite loop because we listen on update here
        $this->traceableFlushSubscriber->onPreUpdate();
    }
}
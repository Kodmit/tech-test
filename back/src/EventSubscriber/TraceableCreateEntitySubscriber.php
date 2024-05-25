<?php

namespace App\EventSubscriber;

use App\Entity\Trace;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use App\Attribute\Traceable;
use Doctrine\ORM\Mapping\PrePersist;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsDoctrineListener(event: Events::prePersist, priority: 500, connection: 'default')]
readonly class TraceableCreateEntitySubscriber
{
    public function __construct(
        private TokenStorageInterface  $tokenStorage,
        private TraceableFlushSubscriber $traceableFlushSubscriber
    ) {}

    #[NoReturn] public function prePersist(PrePersistEventArgs $args): void
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

        if (Traceable::MODE_UPDATE === $traceableAttribute->getWatchMode()) {
            return;
        }

        $objectManager = $args->getObjectManager();

        $className = get_class($args->getObject());
        $watchedProperties = $traceableAttribute->getProperties();

        foreach ($watchedProperties as $watchedProperty) {
            if (false === property_exists($args->getObject(), $watchedProperty)) {
                continue;
            }

            $property = $reflectionClass->getProperty($watchedProperty);
            $value = $property->getValue($args->getObject());

            $trace = new Trace(
                $token->getUser(),
                Trace::ACTION_CREATE,
                $className,
                $watchedProperty,
                null,
                $value
            );

            $objectManager->persist($trace);
        }

        $objectManager->flush();
    }
}
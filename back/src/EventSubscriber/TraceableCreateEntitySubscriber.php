<?php

namespace App\EventSubscriber;

use App\Entity\Trace;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\Collections\Collection;
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
        private TokenStorageInterface  $tokenStorage
    ) {}

    #[NoReturn] public function prePersist(PrePersistEventArgs $args): void
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
        if (Traceable::MODE_UPDATE === $traceableAttribute->getWatchMode()) {
            return;
        }

        $objectManager = $args->getObjectManager();
        $className = get_class($args->getObject());
        $watchedProperties = $traceableAttribute->getProperties();

        foreach ($watchedProperties as $watchedProperty) {
            // Check if the updated property is in the watched properties list, if not continue
            if (false === property_exists($args->getObject(), $watchedProperty)) {
                continue;
            }

            // Get the value of the property
            $property = $reflectionClass->getProperty($watchedProperty);
            $value = $property->getValue($args->getObject());

            if ($value instanceof Collection) {
                continue;
            }

            // Create a trace
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
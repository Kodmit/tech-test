<?php

namespace App\EventSubscriber;

use App\Attribute\Traceable;
use App\Entity\Trace;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsDoctrineListener(event: Events::onFlush, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postFlush, priority: 500, connection: 'default')]
class TraceableRelationsEntitySubscriber
{
    public function __construct(
        private readonly TokenStorageInterface  $tokenStorage,
        private readonly EntityManagerInterface $entityManager,
        private array $traces = []
    ) {}

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        // Get the logged user, leave if there is no connected user
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            return;
        }

        // Foreach on all updated entities collections in the Doctrine unit of work
        foreach ($uow->getScheduledCollectionUpdates() as $collection) {

            // Check for the Traceable attribute in the entity with PHP Reflection and continue if not detected
            $reflectionClass = new \ReflectionClass($collection->getOwner());
            $attributes = $reflectionClass->getAttributes(Traceable::class);
            if ([] === $attributes) {
                continue;
            }

            // Check if in update mode, if not, continue
            $traceableAttribute = $attributes[0]->newInstance();
            if (Traceable::MODE_UPDATE === $traceableAttribute->getWatchMode()) {
                continue;
            }

            // Check if the property is in the watched properties list
            $watchedProperties = $traceableAttribute->getProperties();
            if (false === in_array($collection->getMapping()['fieldName'], $watchedProperties)) {
                continue;
            }

            $this->traceCollectionChanges($collection, $token->getUser());

        }
    }

    // Use this in order to avoid infinite loop when the flush is called, because we listen to the flush in onFlush() function
    public function postFlush(PostFlushEventArgs $args): void
    {
        if ([] !== $this->traces) {
            foreach ($this->traces as $trace) {
                $this->entityManager->persist($trace);
            }
            $this->traces = [];
            $this->entityManager->flush();
        }
    }

    private function traceCollectionChanges(PersistentCollection $collection, UserInterface $user): void
    {
        $entity = $collection->getOwner();
        $mapping = $collection->getMapping();
        $fieldName = $mapping['fieldName'];

        // Get the inserted collections and deleted collections differences in the entity
        $insertDiff = $collection->getInsertDiff();
        $deleteDiff = $collection->getDeleteDiff();

        // Trace additions and create a trace
        foreach ($insertDiff as $item) {
            $trace = new Trace(
                $user,
                Trace::ACTION_ADD_RELATION,
                get_class($entity),
                $fieldName,
                null,
                $item->getId()
            );
            $this->traces[] = $trace;
        }

        // Trace removals and create a trace
        foreach ($deleteDiff as $item) {
            $trace = new Trace(
                $user,
                Trace::ACTION_REMOVE_RELATION,
                get_class($entity),
                $fieldName,
                $item->getId(),
                null
            );
            $this->traces[] = $trace;
        }
    }
}
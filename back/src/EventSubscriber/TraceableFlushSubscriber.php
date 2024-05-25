<?php
namespace App\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::postFlush, priority: 500, connection: 'default')]
class TraceableFlushSubscriber
{
    private bool $needFlush = false;

    public function postFlush(PostFlushEventArgs $args): void
    {
        $entityManager = $args->getObjectManager();

        if (true === $this->needFlush) {
            $this->needFlush = false;
            $entityManager->flush();
        }
    }

    public function onPreUpdate(): void
    {
        $this->needFlush = true;
    }
}

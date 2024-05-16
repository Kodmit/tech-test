<?php

namespace App\MessageHandler;

use App\Message\RemoveContact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

#[AsMessageHandler]
final readonly class RemoveContactHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ContactRepository      $contactRepository
    )
    {}
    public function __invoke(RemoveContact $message): void
    {
        $contact = $this->contactRepository->find($message->getId());

        if (null === $contact) {
            throw new ResourceNotFoundException(sprintf('contact with id "%d" not found.', $message->getId()));
        }

        $this->entityManager->remove($contact);
        $this->entityManager->flush();
    }
}

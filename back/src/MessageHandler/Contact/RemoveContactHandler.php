<?php

namespace App\MessageHandler\Contact;

use App\Message\Contact\RemoveContact;
use App\Repository\ContactRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
        // Check if the contact exist
        $contact = $this->contactRepository->find($message->getContactId());
        if (null === $contact) {
            throw new ResourceNotFoundException(sprintf('contact with id "%d" not found.', $message->getContactId()));
        }

        // Here we create a transaction in order to prevent certain requests from being executed if others fail
        // Either everything succeeds or everything is canceled
        $this->entityManager->beginTransaction();

        try {
            // Retrieve all the tags linked to contact and decrement their count
            foreach ($contact->getTags() as $tag) {
                $tag->setContactsCount($tag->getContactsCount() - 1);
            }

            // Remove the contact
            $this->entityManager->remove($contact);

            // Flush and commit
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Exception $e) {
            // If something fail, we roll back and backup the data
            $this->entityManager->rollBack();
            throw $e;
        }
    }
}

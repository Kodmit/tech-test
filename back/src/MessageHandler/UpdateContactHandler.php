<?php

namespace App\MessageHandler;

use App\Entity\Contact;
use App\Message\UpdateContact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

#[AsMessageHandler]
final readonly class UpdateContactHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ContactRepository      $contactRepository
    ){}
    public function __invoke(UpdateContact $message): Contact
    {
        $contact = $this->contactRepository->find($message->getId());

        if (null === $contact) {
            throw new ResourceNotFoundException(sprintf('contact with id "%d" not found.', $message->getId()));
        }

        $contact->update(
            $message->getLastName(),
            $message->getFirstName(),
            $message->getEmail(),
            $message->getPhoneNumber(),
        );

        $this->entityManager->flush();

        return $contact;
    }
}

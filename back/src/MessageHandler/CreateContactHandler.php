<?php

namespace App\MessageHandler;

use App\Entity\Contact;
use App\Message\CreateContact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateContactHandler
{
    public function __construct(private EntityManagerInterface $entityManager){}
    public function __invoke(CreateContact $message): Contact
    {
        $contact = new Contact(
            $message->getLastName(),
            $message->getFirstName(),
            $message->getEmail(),
            $message->getPhoneNumber()
        );

        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        return $contact;
    }
}

<?php

namespace App\MessageHandler\Contact;

use App\Entity\Contact;
use App\Message\Contact\AddTagToContact;
use App\Repository\ContactRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

#[AsMessageHandler]
final readonly class AddTagToContactHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TagRepository $tagRepository,
        private ContactRepository $contactRepository
    ){}

    public function __invoke(AddTagToContact $command): Contact
    {
        $tag = $this->tagRepository->find($command->getTagId());

        if (null === $tag) {
            throw new ResourceNotFoundException(sprintf(
                'tag with id "%s" not found', $command->getTagId()
            ));
        }

        $contact = $this->contactRepository->find($command->getContactId());

        if (null === $contact) {
            throw new ResourceNotFoundException(sprintf(
                'contact with id "%s" not found', $command->getContactId()
            ));
        }

        $contact->addTag($tag);

        $this->entityManager->flush();

        return $contact;
    }
}
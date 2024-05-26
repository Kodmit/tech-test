<?php

namespace App\Message\Contact;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class RemoveContact
{
    public function __construct(
        #[Assert\Uuid]
        private string $contactId
    ) {}

    public function getContactId(): UuidInterface
    {
        return Uuid::fromString($this->contactId);
    }
}

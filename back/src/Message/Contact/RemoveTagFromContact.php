<?php

namespace App\Message\Contact;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class RemoveTagFromContact
{
    public function __construct(
        private int $tagId,
        #[Assert\Uuid]
        private string $contactId
    )
    {}

    public function getTagId(): int
    {
        return $this->tagId;
    }

    public function getContactId(): UuidInterface
    {
        return Uuid::fromString($this->contactId);
    }
}
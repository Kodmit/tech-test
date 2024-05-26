<?php

namespace App\Message\Contact;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateContact
{
    public function __construct(
        #[Assert\Length(min: 2, max: 255)]
        private string $lastName,
        #[Assert\Length(min: 2, max: 255)]
        private string $firstName,
        #[Assert\Email]
        private string $email,
        #[Assert\Expression(expression: '^\d+$')]
        private string $phoneNumber
    ) {}

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }
}

<?php

namespace App\Message;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateContact
{
    private int $id;
    #[Assert\Length(min: 2, max: 255)]
    private string $lastName;
    #[Assert\Length(min: 2, max: 255)]
    private string $firstName;
    #[Assert\Email]
    private string $email;
    #[Assert\Expression(expression: '^\d+$')]
    private string $phoneNumber;

    public function __construct(int $id, string $lastName, string $firstName, string $email, string $phoneNumber)
    {
        $this->id = $id;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
    }

    public function getId(): int
    {
        return $this->id;
    }

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

<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use App\Attribute\Traceable;
use Doctrine\ORM\Mapping as ORM;

#[Traceable(properties: ['email', 'phoneNumber'], watchMode: Traceable::MODE_BOTH)]
#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $lastName;

    #[ORM\Column(length: 255)]
    private string $firstName;

    #[ORM\Column(length: 255)]
    private string $email;

    #[ORM\Column(length: 10)]
    private string $phoneNumber;

    public function __construct(string $lastName, string $firstName, string $email, string $phoneNumber)
    {
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
    }

    public function update(string $lastName, string $firstName, string $email, string $phoneNumber): void
    {
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

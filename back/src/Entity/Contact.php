<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use App\Attribute\Traceable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[Traceable(properties: ['email', 'phoneNumber', 'tags'], watchMode: Traceable::MODE_BOTH)]
#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[Groups(['contact'])]
    private UuidInterface $id;

    #[ORM\Column(length: 255)]
    #[Groups(['contact'])]
    private string $lastName;

    #[ORM\Column(length: 255)]
    #[Groups(['contact'])]
    private string $firstName;

    #[ORM\Column(length: 255)]
    #[Groups(['contact'])]
    private string $email;

    #[ORM\Column(length: 10)]
    #[Groups(['contact'])]
    private string $phoneNumber;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'contacts')]
    #[Groups(['contact'])]
    private Collection $tags;

    public function __construct(string $lastName, string $firstName, string $email, string $phoneNumber)
    {
        $this->id = Uuid::uuid4();
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->tags = new ArrayCollection();
    }

    public function update(string $lastName, string $firstName, string $email, string $phoneNumber): void
    {
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
    }

    public function getId(): UuidInterface
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

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): void
    {
        if (false === $this->tags->contains($tag)) {
            $this->tags->add($tag);
            $tag->addContact($this);
        }
    }

    public function removeTag(Tag $tag): static
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeContact($this);
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tag'])]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Groups(['tag'])]
    private string $name;

    #[ORM\Column(type: 'integer')]
    #[Groups(['tag'])]
    private int $contactsCount;

    /**
     * @var Collection<int, Contact>
     */
    #[ORM\ManyToMany(targetEntity: Contact::class, inversedBy: 'tags')]
    private Collection $contacts;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->contacts = new ArrayCollection();
        $this->contactsCount = 0;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function getContactsCount(): int
    {
        return $this->contactsCount;
    }

    public function setContactsCount(int $contactCount): void
    {
        $this->contactsCount = $contactCount;
    }

    public function addContact(Contact $contact): void
    {
        if (false === $this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $this->contactsCount += 1;
        }
    }

    public function removeContact(Contact $contact): void
    {
        $this->contacts->removeElement($contact);
        $this->contactsCount -= 1;
    }
}

<?php

namespace App\Entity;

use App\Repository\TraceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TraceRepository::class)]
class Trace
{
    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_ADD_RELATION = 'addRelation';
    public const ACTION_REMOVE_RELATION = 'removeRelation';

    private const AVAILABLE_ACTIONS = [
        self::ACTION_CREATE,
        self::ACTION_UPDATE,
        self::ACTION_ADD_RELATION,
        self::ACTION_REMOVE_RELATION
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\Column(length: 200)]
    private string $action;

    #[ORM\Column(length: 200)]
    private string $className;

    #[ORM\Column(length: 200)]
    private string $property;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $oldValue;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $newValue;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct(User $author, string $action, string $className, string $property, ? string $oldValue, ?string $newValue)
    {
        if (false === in_array($action, self::AVAILABLE_ACTIONS)) {
            throw new \DomainException(sprintf('action "%s" not found', $action));
        }

        $this->author = $author;
        $this->action = $action;
        $this->className = $className;
        $this->property = $property;
        $this->oldValue = $oldValue;
        $this->newValue = $newValue;

        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getOldValue(): ?string
    {
        return $this->oldValue;
    }

    public function getNewValue(): ?string
    {
        return $this->newValue;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}

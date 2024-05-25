<?php
namespace App\Attribute;

use ReflectionClass;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Traceable
{
    public const MODE_CREATE = 'create';
    public const MODE_UPDATE = 'update';
    public const MODE_BOTH = 'both';

    private const AVAILABLE_MODES = [
        self::MODE_CREATE,
        self::MODE_UPDATE,
        self::MODE_BOTH
    ];

    private array $properties;
    private string $watchMode;

    public function __construct(array $properties, string $watchMode = self::MODE_BOTH)
    {
        if (false === in_array($watchMode, self::AVAILABLE_MODES)) {
            throw new \DomainException(sprintf('mode "%s" not found', $watchMode));
        }

        $this->properties = $properties;
        $this->watchMode = $watchMode;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getWatchMode(): string
    {
        return $this->watchMode;
    }
}
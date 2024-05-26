<?php

namespace App\Message\Tag;

final readonly class CreateTag
{
    public function __construct(private string $name) {}

    public function getName(): string
    {
        return $this->name;
    }
}
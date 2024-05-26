<?php

namespace App\Message\Tag;

final readonly class RemoveTag
{
    public function __construct(private int $tagId) {}

    public function getTagId(): int
    {
        return $this->tagId;
    }
}
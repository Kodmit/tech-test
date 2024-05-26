<?php

namespace App\MessageHandler\Tag;

use App\Entity\Tag;
use App\Message\Tag\CreateTag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateTagHandler
{
    public function __construct(private EntityManagerInterface $entityManager){}

    public function __invoke(CreateTag $command): Tag
    {
        $tag = new Tag($command->getName());

        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        return $tag;
    }
}
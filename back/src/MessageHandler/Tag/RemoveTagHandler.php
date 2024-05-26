<?php

namespace App\MessageHandler\Tag;

use App\Message\Tag\RemoveTag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

#[AsMessageHandler]
final readonly class RemoveTagHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TagRepository $tagRepository
    ){}

    public function __invoke(RemoveTag $command): void
    {
        $tag = $this->tagRepository->find($command->getTagId());

        if (null === $tag) {
            throw new ResourceNotFoundException(sprintf(
                'tag with id "%s" not found', $command->getTagId()
            ));
        }

        $this->entityManager->remove($tag);
        $this->entityManager->flush();
    }
}
<?php

namespace App\Controller;

use App\Message\Contact\CreateContact;
use App\Message\Contact\RemoveContact;
use App\Message\Contact\UpdateContact;
use App\Message\Tag\CreateTag;
use App\Message\Tag\RemoveTag;
use App\Repository\ContactRepository;
use App\Repository\TagRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

class TagController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly TagRepository $tagRepository
    ){}
    #[Route('/tags', methods: ['POST'])]
    public function createTag(Request $request): JsonResponse
    {
        $payload = $this->getPayload($request);

        $envelope = $this->commandBus->dispatch(new CreateTag(
            $payload->get('name')
        ));

        return $this->json(
            $envelope->last(HandledStamp::class)->getResult(),
            Response::HTTP_CREATED,
            [],
            ['groups' => ['tag']]
        );
    }

    #[Route('/tags', methods: ['GET'])]
    public function getTags(): JsonResponse
    {
        return $this->json(
            $this->tagRepository->findAll(),
            Response::HTTP_OK,
            [],
            ['groups' => ['tag']]
        );
    }

    #[Route('/tags/{tagId}', methods: ['DELETE'])]
    public function removeTag(int $tagId): JsonResponse
    {
        $this->commandBus->dispatch(new RemoveTag($tagId));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Controller;

use App\Message\Contact\AddTagToContact;
use App\Message\Contact\CreateContact;
use App\Message\Contact\RemoveContact;
use App\Message\Contact\RemoveTagFromContact;
use App\Message\Contact\UpdateContact;
use App\Repository\ContactRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly ContactRepository $contactRepository
    ){}
    #[Route('/contacts', methods: ['POST'])]
    public function createContact(Request $request): JsonResponse
    {
        $payload = $this->getPayload($request);

        $envelope = $this->commandBus->dispatch(new CreateContact(
            $payload->get('lastName'),
            $payload->get('firstName'),
            $payload->get('email'),
            $payload->get('phoneNumber')
        ));

        return $this->json(
            $envelope->last(HandledStamp::class)->getResult(),
            Response::HTTP_CREATED
        );
    }

    #[Route('/contacts', methods: ['GET'])]
    public function getContacts(): JsonResponse
    {
        return $this->json(
            $this->contactRepository->findAll(),
            Response::HTTP_OK,
            [],
            ['groups' => ['contact', 'tag']]
        );
    }

    #[Route('/contacts/{contactId}', methods: ['PUT'])]
    public function updateContact(Request $request, string $contactId): JsonResponse
    {
        $payload = $this->getPayload($request);

        $envelope = $this->commandBus->dispatch(new UpdateContact(
            $contactId,
            $payload->get('lastName'),
            $payload->get('firstName'),
            $payload->get('email'),
            $payload->get('phoneNumber')
        ));

        return $this->json(
            $envelope->last(HandledStamp::class)->getResult(),
            Response::HTTP_OK,
            [],
            ['groups' => ['contact', 'tag']]
        );
    }

    #[Route('/contacts/{contactId}', methods: ['DELETE'])]
    public function removeContact(string $contactId): JsonResponse
    {
        $this->commandBus->dispatch(new RemoveContact($contactId));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/contacts/{contactId}/tags/{tagId}', methods: ['PUT'])]
    public function addTagToContact(string $contactId, int $tagId): JsonResponse
    {
        $envelope = $this->commandBus->dispatch(new AddTagToContact(
            $tagId,
            $contactId
        ));

        return $this->json(
            $envelope->last(HandledStamp::class)->getResult(),
            Response::HTTP_OK,
            [],
            ['groups' => ['contact', 'tag']]
        );
    }

    #[Route('/contacts/{contactId}/tags/{tagId}', methods: ['DELETE'])]
    public function removeTagFromContact(string $contactId, int $tagId): JsonResponse
    {
        $this->commandBus->dispatch(new RemoveTagFromContact(
            $tagId,
            $contactId
        ));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

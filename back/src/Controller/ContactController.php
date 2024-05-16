<?php

namespace App\Controller;

use App\Message\CreateContact;
use App\Message\RemoveContact;
use App\Message\UpdateContact;
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
        return $this->json($this->contactRepository->findAll());
    }

    #[Route('/contacts/{contactId}', methods: ['PUT'])]
    public function updateContact(Request $request, int $contactId): JsonResponse
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
            $envelope->last(HandledStamp::class)->getResult()
        );
    }

    #[Route('/contacts/{contactId}', methods: ['DELETE'])]
    public function removeContact(int $contactId): JsonResponse
    {
        $this->commandBus->dispatch(new RemoveContact($contactId));

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}

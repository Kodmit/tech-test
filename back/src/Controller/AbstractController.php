<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    private array $payload;

    protected function getPayload(Request $request): self
    {
        $this->payload = json_decode($request->getContent(), true);
        return $this;
    }

    protected function get(string $key, bool $nullable = false): mixed
    {
        if (false === isset($this->payload[$key]) && false === $nullable) {
            throw new \DomainException(sprintf('property "%s" is missing', $key));
        }

        return $this->payload[$key] ?? null;
    }
}
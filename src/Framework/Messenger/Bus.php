<?php

declare(strict_types=1);

namespace App\Framework\Messenger;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class Bus
{
    use HandleTrait;

    public function __construct(private MessageBusInterface $bus)
    {
        $this->messageBus = $this->bus;
    }

    public function execute(mixed $usecase): mixed
    {
        return $this->handle($usecase);
    }
}

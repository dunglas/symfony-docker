<?php

namespace App\Framework\Messenger;

use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

abstract class AbstractHandler implements MessageSubscriberInterface
{
    public static function getHandledMessages(): iterable
    {
        yield \sprintf('%sRequest', static::class);
    }
}

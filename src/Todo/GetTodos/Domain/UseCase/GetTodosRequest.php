<?php

declare(strict_types=1);

namespace App\Todo\GetTodos\Domain\UseCase;


final class GetTodosRequest
{

    public function __construct(private readonly int $id)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }
}

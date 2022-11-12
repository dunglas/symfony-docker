<?php

namespace App\Todo\GetTodos\Domain\UseCase;

use App\Framework\Messenger\AbstractHandler;

final class GetTodos extends AbstractHandler
{
    public function __invoke(GetTodosRequest $getTodosRequest)
    {
        dd($getTodosRequest);
    }

}

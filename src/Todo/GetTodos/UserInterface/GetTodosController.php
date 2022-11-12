<?php

declare(strict_types=1);

namespace App\Todo\GetTodos\UserInterface;

use App\Framework\Messenger\Bus;
use App\Todo\GetTodos\Domain\UseCase\GetTodosRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todos', methods: ['GET'])]
final class GetTodosController extends AbstractController
{
    public function __invoke(Bus $bus)
    {
        $bus->execute(new GetTodosRequest(12));
    }
}

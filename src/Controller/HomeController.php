<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\LessonRepository;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(LessonRepository $lessonRepository): Response
    {
        $total = $lessonRepository->countTotal();
        $done = $lessonRepository->countDone();

        return $this->render('home/index.html.twig', [
            'total' => $total,
            'done' => $done,
        ]);
    }
}

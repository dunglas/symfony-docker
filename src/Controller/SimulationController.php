<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;

final class SimulationController extends AbstractController
{
    #[Route('/simulation', name: 'app_simulation')]
    public function index(LessonRepository $lessonRepository): Response
    {
        $lessons = $lessonRepository->findAll();

        return $this->render('simulation/index.html.twig', [
            'lessons' => $lessons,
        ]);
    }

    #[Route('/simulation/{id}', name: 'app_simulation_lesson')]
    public function lesson(int $id, LessonRepository $lessonRepository): Response
    {
        $lesson = $lessonRepository->find($id);

        if (!$lesson) {
            throw $this->createNotFoundException('Lektion nicht gefunden');
        }

        return $this->render('simulation/lesson.html.twig', [
            'lesson' => $lesson,
            'questions' => $lesson->getQuestions(),
        ]);
    }
    
    #[Route('/simulation/{id}/done', name: 'app_simulation_done', methods: ['POST'])]
    public function done(int $id, LessonRepository $lessonRepository, EntityManagerInterface $entityManager): Response
    {
        $lesson = $lessonRepository->find($id);
        $lesson->setDone(true);
        $entityManager->flush();

        return $this->redirectToRoute('app_simulation');
    }
}

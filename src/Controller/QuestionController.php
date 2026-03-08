<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Question;
use App\Repository\QuestionRepository;
use App\Repository\LessonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Form\QuestionType;

final class QuestionController extends AbstractController
{

 #[Route('/question/new', name: 'app_question_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question = new Question();
            $question->setText($form->get('text')->getData());
            $question->setLesson($form->get('lesson')->getData());

            $entityManager->persist($question);
            $entityManager->flush();

            return $this->redirectToRoute('app_question', ['id' => $question->getId()]);
        }

        return $this->render('newQuestion.html.twig', [
            'form' => $form,
        ]);
    }



    #[Route('/question/{id}', name: 'app_question')]
    public function index(EntityManagerInterface  $entityManager, int $id): Response
    {

        $repo = $entityManager->getRepository(Question::class);
        $question = $repo->find($id);
        
        return $this->render('details.html.twig', [
            'question' => $question,
        ]);

    }


    // Suche im Text
    #[Route('/questions/search', name: 'app_questions_search')]
    public function search(Request $request, QuestionRepository $questionRepository): Response
    {
        $search = $request->query->get('q', '');
        $questions = $questionRepository->findByText($search);

        return $this->render('question/search.html.twig', [
            'questions' => $questions,
            'search' => $search,
        ]);
    }

    #[Route('/questions', name: 'app_questions')]
    public function list(Request $request, QuestionRepository $questionRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $questions = $questionRepository->findPaginated(page: $page, limit: 10);

        return $this->render('question/list.html.twig', [
            'questions' => $questions,
            'page' => $page,
        ]);
    }


    #[Route('/question/{id}/delete', name: 'app_question_delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $question = $entityManager->getRepository(Question::class)->find($id);

        if (!$question) {
            throw $this->createNotFoundException('No question found for id ' . $id);
        }

        $entityManager->remove($question);
        $entityManager->flush();

        return $this->redirectToRoute('app_questions');
    }


    #[Route('/question/{id}/edit', name: 'app_question_edit')]
public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
{
    $question = $entityManager->getRepository(Question::class)->find($id);

    if (!$question) {
        throw $this->createNotFoundException('No question found for id ' . $id);
    }

    $form = $this->createForm(QuestionType::class, $question);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $question->setText($form->get('text')->getData());
        $question->setLesson($form->get('lesson')->getData());

        $entityManager->flush();

        return $this->redirectToRoute('app_question', ['id' => $question->getId()]);
    }

    return $this->render('question/edit.html.twig', [
        'form' => $form,
        'question' => $question,
    ]);
}
}

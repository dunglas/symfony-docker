<?php

namespace App\Controller\Admin;

use App\Entity\LandingPageFeedback;
use App\Form\LandingPageFeedbackType;
use App\Repository\LandingPageFeedbackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/landing/page/feedback')]
class LandingPageFeedbackController extends AbstractController
{
    #[Route('/', name: 'app_landing_page_feedback_index', methods: ['GET'])]
    public function index(LandingPageFeedbackRepository $landingPageFeedbackRepository): Response
    {
        return $this->render('landing_page_feedback/index.html.twig', [
            'landing_page_feedbacks' => $landingPageFeedbackRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_landing_page_feedback_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $landingPageFeedback = new LandingPageFeedback();
        $form = $this->createForm(LandingPageFeedbackType::class, $landingPageFeedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($landingPageFeedback);
            $entityManager->flush();

            return $this->redirectToRoute('app_landing_page_feedback_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('landing_page_feedback/new.html.twig', [
            'landing_page_feedback' => $landingPageFeedback,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_landing_page_feedback_show', methods: ['GET'])]
    public function show(LandingPageFeedback $landingPageFeedback): Response
    {
        return $this->render('landing_page_feedback/show.html.twig', [
            'landing_page_feedback' => $landingPageFeedback,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_landing_page_feedback_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LandingPageFeedback $landingPageFeedback, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LandingPageFeedbackType::class, $landingPageFeedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_landing_page_feedback_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('landing_page_feedback/edit.html.twig', [
            'landing_page_feedback' => $landingPageFeedback,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_landing_page_feedback_delete', methods: ['POST'])]
    public function delete(Request $request, LandingPageFeedback $landingPageFeedback, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$landingPageFeedback->getId(), $request->request->get('_token'))) {
            $entityManager->remove($landingPageFeedback);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_landing_page_feedback_index', [], Response::HTTP_SEE_OTHER);
    }
}

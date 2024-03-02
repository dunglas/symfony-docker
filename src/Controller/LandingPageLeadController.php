<?php

namespace App\Controller;

use App\Entity\LandingPageLead;
use App\Form\LandingPageLead1Type;
use App\Repository\LandingPageLeadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/landing/page/lead')]
class LandingPageLeadController extends AbstractController
{
    #[Route('/', name: 'app_landing_page_lead_index', methods: ['GET'])]
    public function index(LandingPageLeadRepository $landingPageLeadRepository): Response
    {
        return $this->render('landing_page_lead/index.html.twig', [
            'landing_page_leads' => $landingPageLeadRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_landing_page_lead_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $landingPageLead = new LandingPageLead();
        $form = $this->createForm(LandingPageLead1Type::class, $landingPageLead);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($landingPageLead);
            $entityManager->flush();

            return $this->redirectToRoute('app_landing_page_lead_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('landing_page_lead/new.html.twig', [
            'landing_page_lead' => $landingPageLead,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_landing_page_lead_show', methods: ['GET'])]
    public function show(LandingPageLead $landingPageLead): Response
    {
        return $this->render('landing_page_lead/show.html.twig', [
            'landing_page_lead' => $landingPageLead,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_landing_page_lead_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LandingPageLead $landingPageLead, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LandingPageLead1Type::class, $landingPageLead);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_landing_page_lead_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('landing_page_lead/edit.html.twig', [
            'landing_page_lead' => $landingPageLead,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_landing_page_lead_delete', methods: ['POST'])]
    public function delete(Request $request, LandingPageLead $landingPageLead, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$landingPageLead->getId(), $request->request->get('_token'))) {
            $entityManager->remove($landingPageLead);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_landing_page_lead_index', [], Response::HTTP_SEE_OTHER);
    }
}

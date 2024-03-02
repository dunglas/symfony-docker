<?php

namespace App\Controller;

use App\Entity\LandingPageFeedback;
use App\Entity\LandingPageLead;
use App\Form\LandingPageFeedbackType;
use App\Form\LandingPageLeadType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LandingPageController extends AbstractController
{
    #[Route('', name: 'app_landing_page')]
    public function index(): Response
    {
        $contactForm = $this->createForm(LandingPageLeadType::class,null,["action" => $this->generateUrl("app_landing_pagelead_submit_contact")]);
        $feedbackForm = $this->createForm(LandingPageFeedbackType::class,null,["action" => $this->generateUrl("app_landing_pagelead_submit_feedback")]);

        return $this->render('landing_page/index.html.twig', [
            'controller_name' => 'LandingPageController',
            'contact_form' => $contactForm->createView(),
            'feedback_form' => $feedbackForm->createView(),
        ]);
    }

    #[Route('/submit/contact', name: 'app_landing_pagelead_submit_contact', methods: ['POST'])]
    public function submit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $landingPageLead = new LandingPageLead();
        $form = $this->createForm(LandingPageLeadType::class, $landingPageLead);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $lead = $form->getData();
            $entityManager->persist($lead);
            $entityManager->flush();
            return $this->json(["message" => "created"]);
        }

        return $this->json(["message"=>"error","errors"=>$form->getErrors()],400);
    }
    #[Route('/submit/feedback', name: 'app_landing_pagelead_submit_feedback', methods: ['POST'])]
    public function submitFeedback(Request $request, EntityManagerInterface $entityManager): Response
    {
        $landingPageFeedback = new LandingPageFeedback();
        $form = $this->createForm(LandingPageFeedbackType::class, $landingPageFeedback);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $lead = $form->getData();
            $entityManager->persist($lead);
            $entityManager->flush();
            return $this->json(["message" => "created"]);
        }

        return $this->json(["message"=>"error","errors"=>$form->getErrors()],400);
    }
}

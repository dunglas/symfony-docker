<?php

namespace App\Controller;

use App\Entity\LandingPageLead;
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
        $form = $this->createForm(LandingPageLeadType::class,null,["action" => $this->generateUrl("app_landing_pagelead_submit")]);

        return $this->render('landing_page/index.html.twig', [
            'controller_name' => 'LandingPageController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/submit', name: 'app_landing_pagelead_submit', methods: ['POST'])]
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
}

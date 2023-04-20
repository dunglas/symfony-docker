<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertiesController extends AbstractController
{
    #[Route('/properties', name: 'app_properties')]
    public function index(): Response
    {
        return $this->render('properties/index.html.twig', [
            'controller_name' => 'PropertiesController',
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Property;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PropertiesController extends AbstractController
{
    #[Route('/properties', name: 'get_properties', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $repository = $em->getRepository(Property::class);
        $properties = $repository->findAll();

        $data = [];

        foreach ($properties as $property) {
            $data[] = [
                'id' => $property->getId(),
                'name' => $property->getName(),
            ];
        }

        return $this->json(
            $data,
            JsonResponse::HTTP_OK, 
            [ 'Access-Control-Allow-Origin' => "*" ]
        );
    }
}

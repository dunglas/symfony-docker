<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\Property;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class JobsController extends AbstractController
{
    #[Route('/jobs', name: 'get_jobs', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $repository = $em->getRepository(Job::class);
        $jobs = $repository->findAll();

        $data = [];

        foreach ($jobs as $job) {
            $property = $job->getProperty();

            $data[] = [
                'id' => $job->getId(),
                'summary' => $job->getSummary(),
                'description' => $job->getDescription(),
                'status' => $job->getStatus(),
                'property' => $property->getName(),
            ];
        }

        return $this->json(
            $data,
            JsonResponse::HTTP_OK, 
            [ 'Access-Control-Allow-Origin' => "*" ]
        );
    }

    #[Route('/jobs', name: 'post_job', methods: ['POST'])]
    public function post(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $repository = $em->getRepository(Job::class);

        $job = new Job();
        $job->setSummary($request->request->get('summary'));
        $job->setDescription($request->request->get('description'));
        $job->setStatus('open');

        $propertyRepository = $em->getRepository(Property::class);
        $property = $propertyRepository->find($request->request->get('property'));

        if (empty($property)) {
            return $this->json(
                ['message' => 'property not found'],
                JsonResponse::HTTP_BAD_REQUEST,
                [ 'Access-Control-Allow-Origin' => "*" ]
            );
        }

        $job->setProperty($property);
        $repository->save($job);

        $data[] = [
            'id' => $job->getId(),
            'summary' => $job->getSummary(),
            'description' => $job->getDescription(),
            'status' => $job->getStatus(),
            'property' => $property->getName(),
        ];

        return $this->json($data);
    }
}

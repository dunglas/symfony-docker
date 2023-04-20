<?php

namespace App\DataFixtures;

use App\Entity\Job;
use App\Entity\Property;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JobFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $job = new Job();
        $job->setSummary('Test Summary');
        $job->setDescription('Test Desc');
        $job->setStatus('Open');

        $property = new Property();
        $property->setName('The House');

        $job->setProperty($property);

        $manager->persist($property);
        $manager->persist($job);

        $manager->flush();
    }
}
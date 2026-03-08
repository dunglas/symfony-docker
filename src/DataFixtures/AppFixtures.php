<?php

namespace App\DataFixtures;

use App\Entity\Question;
use App\Entity\Lesson;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // =========================
        // Benutzer anlegen
        // =========================
        $user1 = new User();
        $user1->setName('JohnDoe');
        $user1->setPassword('password123');
        $manager->persist($user1);

        $user2 = new User();
        $user2->setName('JaneDoe');
        $user2->setPassword('password456');
        $manager->persist($user2);

        // =========================
        // Lektionen + Fragen
        // =========================
        for ($i = 1; $i <= 5; $i++) {

            $lesson = new Lesson();
            $lesson->setTitle("Lesson $i");
            $lesson->setDescription("Description for Lesson $i");
            $lesson->setDuration(45);
            $manager->persist($lesson);

            // 3 Fragen pro Lesson
            for ($j = 1; $j <= 20; $j++) {

                $question = new Question();
                $question->setText("Question $j for Lesson $i");
                $question->setLesson($lesson); // Beziehung setzen
                $manager->persist($question);
            }
        }

        // Alles speichern
        $manager->flush();
    }
}
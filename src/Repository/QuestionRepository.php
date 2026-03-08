<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function findById(int $id): ?Question
    {
        return $this->createQueryBuilder('q')
                ->andWhere('q.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult()
            ;
    }


    public function findByText(string $search): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.text LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('q.id', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findPaginated(int $page = 1, int $limit = 10): array
    {
        return $this->createQueryBuilder('q')
            ->orderBy('q.id', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByLesson(int $lessonId): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.lesson = :lessonId')
            ->setParameter('lessonId', $lessonId)
            ->orderBy('q.id', 'ASC')
            ->getQuery()
            ->getResult();
    }




    //    /**
    //     * @return Question[] Returns an array of Question objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('q')
    //            ->andWhere('q.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('q.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Question
    //    {
    //        return $this->createQueryBuilder('q')
    //            ->andWhere('q.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

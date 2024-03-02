<?php

namespace App\Repository;

use App\Entity\LandingPageFeedback;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LandingPageFeedback>
 *
 * @method LandingPageFeedback|null find($id, $lockMode = null, $lockVersion = null)
 * @method LandingPageFeedback|null findOneBy(array $criteria, array $orderBy = null)
 * @method LandingPageFeedback[]    findAll()
 * @method LandingPageFeedback[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LandingPageFeedbackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LandingPageFeedback::class);
    }

    //    /**
    //     * @return LandingPageFeedback[] Returns an array of LandingPageFeedback objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?LandingPageFeedback
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

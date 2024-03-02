<?php

namespace App\Repository;

use App\Entity\LandingPageLead;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LandingPageLead>
 *
 * @method LandingPageLead|null find($id, $lockMode = null, $lockVersion = null)
 * @method LandingPageLead|null findOneBy(array $criteria, array $orderBy = null)
 * @method LandingPageLead[]    findAll()
 * @method LandingPageLead[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LandingPageLeadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LandingPageLead::class);
    }

    //    /**
    //     * @return LandingPageLead[] Returns an array of LandingPageLead objects
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

    //    public function findOneBySomeField($value): ?LandingPageLead
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

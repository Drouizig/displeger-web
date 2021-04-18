<?php

namespace App\Repository;

use App\Entity\DescriptionTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DescriptionTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method DescriptionTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method DescriptionTranslation[]    findAll()
 * @method DescriptionTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DescriptionTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DescriptionTranslation::class);
    }

    // /**
    //  * @return DescriptionTranslation[] Returns an array of DescriptionTranslation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DescriptionTranslation
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\TranslatorUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TranslatorUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method TranslatorUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method TranslatorUser[]    findAll()
 * @method TranslatorUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TranslatorUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TranslatorUser::class);
    }

    // /**
    //  * @return TranslatorUser[] Returns an array of TranslatorUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TranslatorUser
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

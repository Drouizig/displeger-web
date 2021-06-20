<?php

namespace App\Repository;

use App\Entity\TranslatableText;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @method TranslatableText|null find($id, $lockMode = null, $lockVersion = null)
 * @method TranslatableText|null findOneBy(array $criteria, array $orderBy = null)
 * @method TranslatableText[]    findAll()
 * @method TranslatableText[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TranslatableTextRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TranslatableText::class);
    }

    // /**
    //  * @return TranslatableText[] Returns an array of TranslatableText objects
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
    public function findOneBySomeField($value): ?TranslatableText
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

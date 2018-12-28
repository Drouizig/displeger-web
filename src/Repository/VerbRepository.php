<?php

namespace App\Repository;

use App\Entity\Verb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Verb|null find($id, $lockMode = null, $lockVersion = null)
 * @method Verb|null findOneBy(array $criteria, array $orderBy = null)
 * @method Verb[]    findAll()
 * @method Verb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerbRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Verb::class);
    }

    public function findByTerm($term)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('UPPER(v.anvVerb) LIKE UPPER(:term)')
            ->setParameter('term', $term.'%')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByAnvVerb($anvVerb)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('UPPER(v.anvVerb) = UPPER(:anvVerb)')
            ->setParameter('anvVerb', $anvVerb)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

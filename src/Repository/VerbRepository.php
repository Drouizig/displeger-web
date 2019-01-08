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

    public function getAllVerbQuery()
    {
        return $this->createQueryBuilder('v')
        ->addOrderBy('v.category')
        ->addOrderBy('v.anvVerb')
        ->addOrderBy('v.pennrann')
        ->getQuery();
    }

    public function getSearchQuery($search)
    {
        return $this->createQueryBuilder('v')
        ->where('v.anvVerb LIKE :term')
        ->orWhere('v.pennrann LIKE :term')
        ->orWhere('v.category LIKE :term')
        ->orWhere('v.galleg LIKE :term')
        ->orWhere('v.saozneg LIKE :term')
        ->setParameter('term', '%'.$search.'%')
        ->getQuery();
    }
}

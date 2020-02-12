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

    public function findByTermAutocomplete($term)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('UPPER(v.anvVerb) LIKE UPPER(:term)')
            ->setParameter('term', $term.'%')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getFrontSearchQuery($term)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('UPPER(v.anvVerb) LIKE UPPER(:term)')
            ->setParameter('term', '%'.$term.'%')
            ->getQuery()
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

    public function getBackSearchQuery($search, $offset = null, $maxResults = null)
    {
        $qb = $this->createQueryBuilder('v');
        if ($search !== null && $search !== '') {
            $qb
                ->where('v.anvVerb LIKE :term')
                ->orWhere('v.pennrann LIKE :term')
                ->orWhere('v.category LIKE :term')
                ->orWhere('v.galleg LIKE :term')
                ->orWhere('v.saozneg LIKE :term')
                ->setParameter('term', '%'.$search.'%')
            ;
        }
        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }
        if ($maxResults !== null) {
            $qb->setMaxResults($maxResults);
        }
        $qb
            ->addOrderBy('v.category')
            ->addOrderBy('v.anvVerb')
            ->addOrderBy('v.pennrann')
        ;
        return $qb->getQuery();
    }

    public function findCategoryStatistics()
    {
        return $this->createQueryBuilder('v')
            ->select('v.category as name, count(v.anvVerb) as y')
            ->groupBy('v.category')
            ->getQuery()
            ->getResult();
    }

    public function findRandomVerb(){
        $allIds = $this->createQueryBuilder('v')
            ->select('v.id')
            ->getQuery()
            ->getArrayResult();

        if(sizeof($allIds) == 0) {
            $verb = null;
        } else {
            $randomIdx = random_int(0, sizeof($allIds)-1);
            $verb = $this->findOneBy(['id' => $allIds[$randomIdx]]);
        }

        return $verb;
    }
}

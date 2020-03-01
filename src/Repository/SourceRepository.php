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
class SourceRepository extends ServiceEntityRepository implements AdminRepositoryInterface
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Verb::class);
    }

    
    public function getBackSearchQuery($search, $offset = null, $maxResults = null)
    {
        $qb = $this->createQueryBuilder('v');
        $qb->join('v.localizations', 'l');
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
            //->addOrderBy('l.infinitive')
            // ->addOrderBy('v.pennrann')
        ;
        return $qb->getQuery();
    }

}

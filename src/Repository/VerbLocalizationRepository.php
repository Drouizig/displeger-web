<?php

namespace App\Repository;

use App\Entity\VerbLocalization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
/**
 * @method Verb|null find($id, $lockMode = null, $lockVersion = null)
 * @method Verb|null findOneBy(array $criteria, array $orderBy = null)
 * @method Verb[]    findAll()
 * @method Verb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerbLocalizationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VerbLocalization::class);
    }

    public function findByTermAutocomplete($term)
    {
        $escapedTerm = str_replace('n', '_', $term);
        return $this->createQueryBuilder('vt')
            ->andWhere('UPPER(vt.infinitive) LIKE UPPER(:term)')
            ->setParameter('term', $escapedTerm.'%')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getFrontSearchQuery($term)
    {
        $escapedTerm = str_replace('n', '_', $term);
        return $this->createQueryBuilder('vt')
            ->andWhere('UPPER(vt.infinitive) LIKE UPPER(:term)')
            ->setParameter('term', '%'.$escapedTerm.'%')
            ->getQuery()
        ;
    }

    public function findCategoryStatistics()
    {
        return $this->createQueryBuilder('vl')
            ->select('vl.category as name, count(vl.id) as y')
            ->groupBy('vl.category')
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

    public function getNextVerb($infinitive)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('UPPER(v.infinitive) > UPPER(:infinitive)')
            ->setParameter('infinitive', $infinitive)
            ->addOrderBy('v.infinitive', 'ASC')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
            ;
    }

    public function getPreviousVerb($infinitive)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('UPPER(v.infinitive) < UPPER(:infinitive)')
            ->setParameter('infinitive', $infinitive)
            ->orderBy('v.infinitive', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
            ;
    }

    public function findOneByVerbId($verbId)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.verb = :verbId')
            ->setParameter('verbId', $verbId)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
            ;
    }

}

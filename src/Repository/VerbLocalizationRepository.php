<?php

namespace App\Repository;

use App\Entity\VerbLocalization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
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
        return $this->getFrontSearchQueryBuilder($term)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getFrontSearchQuery($term)
    {
        return $this->getFrontSearchQueryBuilder($term)
            ->getQuery()
        ;
    }
    
    public function getFrontSearchQueryBuilder($term)
    {
        $term = str_replace('Ñ', 'ñ', strtolower($term));
        
        $possibilities = self::get_all_posibilities($term);
        unset($possibilities[0]);
        $qb = $this->createQueryBuilder('vl')
            ->addSelect('CASE WHEN LOWER(vl.infinitive) LIKE :term_begin THEN 1 ELSE 0 END AS HIDDEN sortCondition')
            ->innerJoin('vl.verb', 'v')
            ->andWhere('v.enabled = true')
            ->andWhere('LOWER(vl.infinitive) LIKE :term');
            foreach($possibilities as $i => $possibility) {
                $qb->orWhere('LOWER(vl.infinitive) LIKE :term'.$i);
                $qb->setParameter(':term'.$i, '%'.$possibility.'%');
            }
        $qb->setParameter('term', '%'.$term.'%')
           ->setParameter('term_begin', $term.'%')
           ->orderBy('sortCondition', 'DESC');

        return $qb;
        ;
    }

    static function get_all_posibilities($term)
    {
        $tmpRerm = $term;
        $indexes = [];
        $i = 0; // Limit the number of N to replace to 6 otherwise the request is too big
        while(($pos = strpos($tmpRerm, 'n')) !== false && $i<= 6) {
            $indexes[] = $pos;
            $tmpRerm[$pos] = '_';
            $i++;
        }

        $combinations = self::all_combinations($indexes);

        $possibilities = [];
        foreach($combinations as $combination) {
            $newTerm = $term;
            foreach($combination as $pos) {
                $newTerm = substr_replace($newTerm, 'ñ', $pos).substr($newTerm, $pos+1);
            }
            $possibilities[] = $newTerm;
        }

        return $possibilities;
    }

    static function all_combinations($array) {
        $results = [[]];
        foreach ($array as $element)
            foreach ($results as $combination)
                array_push($results, array_merge([$element], $combination));
    
        return $results;
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

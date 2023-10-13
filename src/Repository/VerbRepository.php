<?php

namespace App\Repository;

use App\Entity\Verb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @method Verb|null find($id, $lockMode = null, $lockVersion = null)
 * @method Verb|null findOneBy(array $criteria, array $orderBy = null)
 * @method Verb[]    findAll()
 * @method Verb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerbRepository extends ServiceEntityRepository implements AdminRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
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
        $qb->select('v verb, l.infinitive li, l.base lb');
        $qb->leftJoin('v.localizations', 'l');
        if ($search !== null && $search !== '') {
            $qb->leftJoin('v.translations', 't');
            $qb
                ->where('l.infinitive LIKE :term')
                ->orWhere('l.base LIKE :term')
                ->orWhere('l.category LIKE :term')
                ->orWhere('t.translation LIKE :term')
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
            ->addOrderBy('li')
            ->addOrderBy('lb')
        ;
        return $qb->getQuery();
    }


    public function getLocalizationSearchBuilder($term)
    {
        $escapedTerm = str_replace('n', '_', $term);
        return $this->createQueryBuilder('v')
            ->leftJoin('v.localizations', 'vl')
            ->andWhere('UPPER(vl.infinitive) LIKE UPPER(:term)')
            ->setParameter('term', '%'.$escapedTerm.'%')
        ;
    }

    public function getTranslationSearchBuilder($term, $language)
    {
        return $this->createQueryBuilder('v')
            ->addSelect('LOCATE(UPPER(:term), UPPER(vt.translation)) idx,
             CASE WHEN LOCATE(\',\', vt.translation) = 0 THEN LENGTH(vt.translation) ELSE LOCATE(\',\', vt.translation) END comma, 
             LENGTH(vt.translation) len')
            ->leftJoin('v.translations', 'vt')
            ->where('UPPER(vt.translation) LIKE UPPER(:termWildcard)')
            ->andWhere('vt.languageCode = :language')
            ->orderBy('idx, comma, len')
            ->setParameter('language', $language)
            ->setParameter('termWildcard', '%'.$term.'%')
            ->setParameter('term', $term)
        ;
    }

}

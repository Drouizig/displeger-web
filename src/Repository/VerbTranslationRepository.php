<?php

namespace App\Repository;

use App\Entity\VerbTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @method Verb|null find($id, $lockMode = null, $lockVersion = null)
 * @method Verb|null findOneBy(array $criteria, array $orderBy = null)
 * @method Verb[]    findAll()
 * @method Verb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerbTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VerbTranslation::class);
    }

    public function getFrontSearchQuery($term, $language): Query
    {
        return $this->createQueryBuilder('vt')
            ->andWhere('UPPER(vt.translation) LIKE UPPER(:term)')
            ->andWhere('vt.languageCode = :language')
            ->setParameter('term', '%'.$term.'%')
            ->setParameter('language', $language)
            ->getQuery()
        ;
    }

}

<?php

namespace App\Repository;

use App\Entity\VerbLocalization;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Verb|null find($id, $lockMode = null, $lockVersion = null)
 * @method Verb|null findOneBy(array $criteria, array $orderBy = null)
 * @method Verb[]    findAll()
 * @method Verb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerbLocalizationRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, VerbLocalization::class);
    }

    public function findCategoryStatistics()
    {
        return $this->createQueryBuilder('vl')
            ->select('vl.category as name, count(vl.id) as y')
            ->groupBy('vl.category')
            ->getQuery()
            ->getResult();
    }

}

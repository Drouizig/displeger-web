<?php

namespace App\Repository;

use App\Entity\ConfigurationTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ConfigurationTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfigurationTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfigurationTranslation[]    findAll()
 * @method ConfigurationTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfigurationTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfigurationTranslation::class);
    }

    public function findByCodeAndLocale($code, $locale)
    {
        return $this->createQueryBuilder('ct')
            ->leftJoin('ct.configuration', 'c')
            ->where('c.code = :code')
            ->andWhere('ct.locale = :locale')
            ->setParameter('code', $code)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getSingleResult()
        ;
    }
}

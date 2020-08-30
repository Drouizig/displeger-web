<?php

namespace App\Repository;

use App\Entity\TagTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TagTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TagTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TagTranslation[]    findAll()
 * @method TagTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TagTranslation::class);
    }
}

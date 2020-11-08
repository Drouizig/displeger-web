<?php

namespace App\Repository;

use App\Entity\TagCategoryTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TagCategoryTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TagCategoryTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TagCategoryTranslation[]    findAll()
 * @method TagCategoryTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagCategoryTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TagCategoryTranslation::class);
    }
}

<?php

namespace App\Repository;

use App\Entity\VerbTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method VerbTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method VerbTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method VerbTag[]    findAll()
 * @method VerbTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VerbTagRepository extends ServiceEntityRepository 
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VerbTag::class);
    }

}

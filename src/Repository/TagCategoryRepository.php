<?php

namespace App\Repository;

use App\Entity\TagCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TagCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method TagCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method TagCategory[]    findAll()
 * @method TagCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TagCategory::class);
    }

    public function getBackSearchQuery($search, $offset = null, $maxResults = null)
    {
        $qb = $this->createQueryBuilder('s');
        if ($search !== null && $search !== '') {
            $qb->join('s.translations', 't');
            $qb
                ->where('s.code LIKE :term')
                ->orWhere('t.label LIKE :term')
                ->orWhere('t.description LIKE :term')
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
            ->addOrderBy('s.code')
        ;
        return $qb->getQuery();
    }
}

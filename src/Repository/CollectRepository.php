<?php

namespace App\Repository;

use App\Entity\Collect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Collect>
 */
class CollectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collect::class);
    }
    
    public function findAllWithCategories()
    {
        return $this->createQueryBuilder('c')
            ->addSelect('cc')
            ->leftJoin('c.categorieCollect', 'cc')
            ->getQuery()
            ->getResult();
    }




}

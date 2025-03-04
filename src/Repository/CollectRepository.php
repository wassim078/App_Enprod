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

    public function search(string $input)
    {
        return $this->createQueryBuilder('c')
            ->addSelect('cc')
            ->leftJoin('c.categorieCollect', 'cc')
            ->where('c.titre LIKE :input')
            ->orWhere('c.nomProduit LIKE :input')
            ->orWhere('c.lieu LIKE :input')
            ->orWhere('cc.nom LIKE :input')
            ->setParameter('input', '%'.$input.'%')
            ->getQuery()
            ->getResult();
    }

}

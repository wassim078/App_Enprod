<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    /**
     * Fetch all annonces with their category in one go.
     *
     * @return Annonce[]
     */
    public function findAllWithCategory(): array
    {
        return $this->createQueryBuilder('a')
            ->addSelect('c')
            ->leftJoin('a.categorieAnnonce', 'c')
            ->getQuery()
            ->getResult();
    }

    public function findAllWithCategoryAndUser(): array
    {
        return $this->createQueryBuilder('a')
            ->addSelect('c', 'u')
            ->leftJoin('a.categorieAnnonce', 'c')
            ->leftJoin('a.user', 'u')
            ->getQuery()
            ->getResult();
    }

}

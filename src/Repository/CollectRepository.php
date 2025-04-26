<?php

// src/Repository/CollectRepository.php
namespace App\Repository;

use App\Entity\Collect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CollectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collect::class);
    }

    public function findByUser($user)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findAllCollects()
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.user', 'u')
            ->addSelect('u')
            ->leftJoin('c.categorieCollect', 'cc')
            ->addSelect('cc')
            ->orderBy('c.dateDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
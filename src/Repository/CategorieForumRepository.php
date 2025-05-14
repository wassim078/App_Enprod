<?php

// src/Repository/CategorieForumRepository.php

namespace App\Repository;

use App\Entity\CategorieForum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategorieForumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieForum::class);
    }
}
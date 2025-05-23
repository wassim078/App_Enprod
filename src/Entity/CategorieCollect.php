<?php

// src/Entity/CategorieCollect.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategorieCollectRepository;

#[ORM\Entity(repositoryClass: CategorieCollectRepository::class)]
#[ORM\Table(name: 'categorie_collect')]
class CategorieCollect
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
}
<?php

// src/Entity/Collect.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CollectRepository;
use App\Entity\User;
use App\Entity\CategorieCollect;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CollectRepository::class)]
#[ORM\Table(name: 'collect')]
class Collect
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: CategorieCollect::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategorieCollect $categorieCollect = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $nomProduit = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?int $quantite = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    private ?string $lieu = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Assert\GreaterThan('today')]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function getCategorieCollect(): ?CategorieCollect { return $this->categorieCollect; }
    public function setCategorieCollect(?CategorieCollect $categorieCollect): self { $this->categorieCollect = $categorieCollect; return $this; }
    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }
    public function getNomProduit(): ?string { return $this->nomProduit; }
    public function setNomProduit(string $nomProduit): self { $this->nomProduit = $nomProduit; return $this; }
    public function getQuantite(): ?int { return $this->quantite; }
    public function setQuantite(int $quantite): self { $this->quantite = $quantite; return $this; }
    public function getLieu(): ?string { return $this->lieu; }
    public function setLieu(string $lieu): self { $this->lieu = $lieu; return $this; }
    public function getDateDebut(): ?\DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(\DateTimeInterface $dateDebut): self { $this->dateDebut = $dateDebut; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }
}
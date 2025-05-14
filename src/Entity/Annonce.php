<?php
namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
#[ORM\Table(name: 'annonce')]
class Annonce
{
#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column]
private ?int $id = null;

#[ORM\ManyToOne(inversedBy: 'annonces')]
#[ORM\JoinColumn(name: 'categorie_annonce_id', referencedColumnName: 'id', nullable: false)]
private ?CategorieAnnonce $categorieAnnonce = null;


#[ORM\ManyToOne]
#[ORM\JoinColumn(nullable: false)]
private ?User $user = null;

#[ORM\Column(length: 255)]
private ?string $titre = null;

#[ORM\Column]
private ?float $poids = null;

#[ORM\Column]
private ?float $prix = null;

#[ORM\Column(type: 'text')]
private ?string $description = null;

#[ORM\Column(length: 255)]
private ?string $image = null;

#[ORM\Column]
private ?int $quantite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getCategorieAnnonce(): ?CategorieAnnonce
    {
        return $this->categorieAnnonce;
    }

    public function setCategorieAnnonce(?CategorieAnnonce $categorieAnnonce): self
    {
        $this->categorieAnnonce = $categorieAnnonce;
        return $this;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getPoids(): ?float
    {
        return $this->poids;
    }

    public function setPoids(?float $poids): void
    {
        $this->poids = $poids;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): void
    {
        $this->titre = $titre;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): void
    {
        $this->prix = $prix;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): void
    {
        $this->quantite = $quantite;
    }

// Add getters and setters for all properties
}
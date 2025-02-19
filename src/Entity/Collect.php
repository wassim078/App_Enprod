<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\CollectRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollectRepository::class)]
class Collect
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "The title is required.")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "The title must be at least {{ limit }} characters long.",
        maxMessage: "The title cannot be longer than {{ limit }} characters."
    )]
    private string $titre;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "The product name is required.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "The product name must be at least {{ limit }} characters long.",
        maxMessage: "The product name cannot be longer than {{ limit }} characters."
    )]
    private string $nomProduit;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: "The quantity is required.")]
    #[Assert\Positive(message: "The quantity must be a positive number.")]
    private int $quantite;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "The location is required.")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "The location must be at least {{ limit }} characters long.",
        maxMessage: "The location cannot be longer than {{ limit }} characters."
    )]
    private string $lieu;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: "The date is required.")]
    #[Assert\GreaterThan("today", message: "The date must be in the future.")]
    private \DateTimeInterface $dateDebut;


    #[ORM\ManyToOne(targetEntity: CategorieCollect::class, inversedBy: 'collects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CategorieCollect $categorieCollect = null;
    

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getNomProduit(): ?string
    {
        return $this->nomProduit;
    }

    public function setNomProduit(string $nomProduit): static
    {
        $this->nomProduit = $nomProduit;

        return $this;
    }

    public function getQuantite(): ?float
    {
        return $this->quantite;
    }

    public function setQuantite(float $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getCategorieCollect(): ?CategorieCollect
    {
        return $this->categorieCollect;
    }

    public function setCategorieCollect(?CategorieCollect $categorieCollect): static
    {
        $this->categorieCollect = $categorieCollect;

        return $this;
    }
}

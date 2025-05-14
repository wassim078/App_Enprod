<?php

// src/Entity/Reclamation.php
namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
#[Vich\Uploadable]
#[ORM\Table(name: 'reclamation')]
class Reclamation
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    private ?string $sujet = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Choice([
        "Technical Issue",
        "Billing Problem",
        "Service Complaint",
        "Other"
    ])]
    private ?string $type = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice([
        "En Attente",
        "En Cours",
        "Résolue",
        "Rejetée"
    ])]
    private ?string $etat = 'En Attente';

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date = null;



    #[Vich\UploadableField(mapping: "reclamation_files", fileNameProperty: "fileName")]
    private ?File $file = null;

    #[ORM\Column(length: 255, nullable: true, name: 'file')]
    private ?string $fileName = null;

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }
    public function getSujet(): ?string { return $this->sujet; }
    public function setSujet(string $sujet): self { $this->sujet = $sujet; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }
    public function getType(): ?string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }
    public function getEtat(): ?string { return $this->etat; }
    public function setEtat(string $etat): self { $this->etat = $etat; return $this; }
    public function getDate(): ?\DateTimeInterface { return $this->date; }
    public function setDate(\DateTimeInterface $date): self { $this->date = $date; return $this; }
    public function getFile(): ?File { return $this->file; }
    public function setFile(?File $file = null): void
    {
        $this->file = $file;
        if (null !== $file) {
            $this->date = new \DateTimeImmutable();
        }
    }
    public function getFileName(): ?string { return $this->fileName; }
    public function setFileName(?string $fileName): self { $this->fileName = $fileName; return $this; }

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->etat = 'En Attente';
    }
}
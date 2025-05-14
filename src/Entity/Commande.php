<?php

// src/Entity/Commande.php
namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\Table(name: 'commande')]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(name: 'client_name', type: Types::STRING, length: 255)]
    private string $clientName;

    #[ORM\Column(name: 'client_family_name', type: Types::STRING, length: 255)]
    private string $clientFamilyName;

    #[ORM\Column(name: 'client_adresse', type: Types::STRING, length: 255)]
    private string $clientAdresse;

    #[ORM\Column(name: 'client_phone', type: Types::STRING, length: 255)]
    private string $clientPhone;

    #[ORM\Column(name: 'annonce_quantities', type: Types::JSON, nullable: true)]
    private ?array $annonceQuantities = null;

    #[ORM\Column(name: 'methode_paiement', type: Types::STRING, length: 255)]
    private string $methodePaiement;

    #[ORM\Column(name: 'etat_commande', type: Types::STRING, length: 255)]
    private string $etatCommande = 'En attente';

    #[ORM\Column(name: 'instruction_speciale', type: Types::STRING, length: 255, nullable: true)]
    private ?string $instructionSpeciale = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $prixtotal;

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getClientName(): string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): self
    {
        $this->clientName = $clientName;
        return $this;
    }

    public function getClientFamilyName(): string
    {
        return $this->clientFamilyName;
    }

    public function setClientFamilyName(string $clientFamilyName): self
    {
        $this->clientFamilyName = $clientFamilyName;
        return $this;
    }

    public function getClientAdresse(): string
    {
        return $this->clientAdresse;
    }

    public function setClientAdresse(string $clientAdresse): self
    {
        $this->clientAdresse = $clientAdresse;
        return $this;
    }

    public function getClientPhone(): string
    {
        return $this->clientPhone;
    }

    public function setClientPhone(string $clientPhone): self
    {
        $this->clientPhone = $clientPhone;
        return $this;
    }

    public function getAnnonceQuantities(): ?array
    {
        return $this->annonceQuantities;
    }

    public function setAnnonceQuantities(?array $annonceQuantities): self
    {
        $this->annonceQuantities = $annonceQuantities;
        return $this;
    }

    public function getMethodePaiement(): string
    {
        return $this->methodePaiement;
    }

    public function setMethodePaiement(string $methodePaiement): self
    {
        $this->methodePaiement = $methodePaiement;
        return $this;
    }

    public function getEtatCommande(): string
    {
        return $this->etatCommande;
    }

    public function setEtatCommande(string $etatCommande): self
    {
        $this->etatCommande = $etatCommande;
        return $this;
    }

    public function getInstructionSpeciale(): ?string
    {
        return $this->instructionSpeciale;
    }

    public function setInstructionSpeciale(?string $instructionSpeciale): self
    {
        $this->instructionSpeciale = $instructionSpeciale;
        return $this;
    }

    public function getPrixtotal(): int
    {
        return $this->prixtotal;
    }

    public function setPrixtotal(int $prixtotal): self
    {
        $this->prixtotal = $prixtotal;
        return $this;
    }
}
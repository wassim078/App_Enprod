<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    public function __construct()
    {
        $this->annonces = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message:'Client name is required')]
    #[ORM\Column(length: 255)]
    private ?string $clientName = null;

    #[ORM\Column(length: 255)]
    private ?string $clientFamilyName = null;

    #[Assert\NotBlank(message:'Client address is required')]
    #[ORM\Column(length: 255)]
    private ?string $clientAdresse = null;

    #[Assert\NotBlank(message:'Client phone is required')]
    #[ORM\Column(length: 255)]
    private ?string $clientPhone = null;
    
    #[ORM\Column(type: "json", nullable: true)]
    private ?array $annonceQuantities = null;
    
    #[Assert\NotBlank(message:'Method of payment is required')]
    #[Assert\Choice(choices: ['à la livraison', 'e-paiement'])]
    #[ORM\Column(length: 255)]
    private ?string $methodePaiement = null;

    #[Assert\NotBlank(message:'Order state is required')]
    #[ORM\Column(length: 255)]
    private ?string $etatCommande = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $instructionSpeciale = null;

    #[ORM\ManyToMany(targetEntity: Annonce::class, mappedBy: 'commandes')]
    private Collection $annonces;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?User $User = null;

    #[ORM\Column]
    private ?int $prixtotal = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(?string $clientName): static
    {
        $this->clientName = $clientName;
        return $this;
    }

    public function getClientFamilyName(): ?string
    {
        return $this->clientFamilyName;
    }

    public function setClientFamilyName(?string $clientFamilyName): static
    {
        $this->clientFamilyName = $clientFamilyName;
        return $this;
    }

    public function getClientAdresse(): ?string
    {
        return $this->clientAdresse;
    }

    public function setClientAdresse(?string $clientAdresse): static
    {
        $this->clientAdresse = $clientAdresse;
        return $this;
    }

    public function getClientPhone(): ?string
    {
        return $this->clientPhone;
    }

    public function setClientPhone(?string $clientPhone): static
    {
        $this->clientPhone = $clientPhone;
        return $this;
    }

    public function getAnnonceQuantities(): ?array
    {
        return $this->annonceQuantities;
    }

    public function setAnnonceQuantities(?array $annonceQuantities): static
    {
        $this->annonceQuantities = $annonceQuantities;
        return $this;
    }

    public function getMethodePaiement(): ?string
    {
        return $this->methodePaiement;
    }

    public function setMethodePaiement(?string $methodePaiement): static
    {
        $this->methodePaiement = $methodePaiement;
        return $this;
    }

    public function getEtatCommande(): ?string
    {
        return $this->etatCommande;
    }

    public function setEtatCommande(?string $etatCommande): static
    {
        $this->etatCommande = $etatCommande;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getInstructionSpeciale(): ?string
    {
        return $this->instructionSpeciale;
    }

    public function setInstructionSpeciale(?string $instructionSpeciale): static
    {
        $this->instructionSpeciale = $instructionSpeciale;
        return $this;
    }

    /**
     * @return Collection<int, Annonce>
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonce(Annonce $annonce): static
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces->add($annonce);
            $annonce->addCommande($this);
        }
        return $this;
    }

    public function removeAnnonce(Annonce $annonce): static
    {
        if ($this->annonces->removeElement($annonce)) {
            $annonce->removeCommande($this);
        }
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;
        return $this;
    }

    public function getPrixtotal(): ?int
    {
        return $this->prixtotal;
    }

    public function setPrixtotal(int $prixtotal): static
    {
        $this->prixtotal = $prixtotal;
        return $this;
    }
}
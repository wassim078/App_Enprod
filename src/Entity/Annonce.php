<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity]
class Annonce
{
#[ORM\Id]
#[ORM\GeneratedValue]//autoincrement
#[ORM\Column]
private ?int $id = null;

#[ORM\Column(type: Types::STRING, length: 100)]
#[Assert\NotBlank]
#[Assert\Length(max: 100)]
private ?string $titre = null;

#[ORM\Column(type: Types::FLOAT)]
#[Assert\Positive]
private ?float $poids = null;

#[ORM\Column(type: Types::FLOAT)]
#[Assert\Positive]
private ?float $prix = null;

#[ORM\Column(type: Types::TEXT)]
#[Assert\NotBlank]
private ?string $description = null;

#[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
private ?string $image = null;

private ?File $imageFile = null;

#[ORM\ManyToOne(targetEntity: Category::class)]
#[ORM\JoinColumn(nullable: false)]
#[Assert\NotBlank]
private ?Category $category = null;

#[ORM\Column(type: "datetime", nullable: true)]
private ?\DateTimeInterface $updatedAt = null;

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

public function getPoids(): ?float
{
return $this->poids;
}

public function setPoids(float $poids): static
{
$this->poids = $poids;
return $this;
}

public function getPrix(): ?float
{
return $this->prix;
}

public function setPrix(float $prix): static
{
$this->prix = $prix;
return $this;
}

public function getDescription(): ?string
{
return $this->description;
}

public function setDescription(string $description): static
{
$this->description = $description;
return $this;
}

public function getImage(): ?string
{
return $this->image;
}

public function setImage(?string $image): static
{
$this->image = $image;
return $this;
}

public function getImageFile(): ?File
{
return $this->imageFile;
}

public function setImageFile(?File $imageFile): void
{
$this->imageFile = $imageFile;
if ($imageFile) {
$this->updatedAt = new \DateTimeImmutable();
}
}

public function getCategory(): ?Category
{
return $this->category;
}

public function setCategory(?Category $category): static
{
$this->category = $category;
return $this;
}
}

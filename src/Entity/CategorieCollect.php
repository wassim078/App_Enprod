<?php

namespace App\Entity;

use App\Repository\CategorieCollectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieCollectRepository::class)]
class CategorieCollect
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    /**
     * @var Collection<int, Collect>
     */
    #[ORM\OneToMany(targetEntity: Collect::class, mappedBy: 'categorieCollect')]
    private Collection $collects;

    public function __construct()
    {
        $this->collects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    /**
     * @return Collection<int, Collect>
     */
    public function getCollects(): Collection
    {
        return $this->collects;
    }

    public function addCollect(Collect $collect): static
    {
        if (!$this->collects->contains($collect)) {
            $this->collects->add($collect);
            $collect->setCategorieCollect($this);
        }

        return $this;
    }

    public function removeCollect(Collect $collect): static
    {
        if ($this->collects->removeElement($collect)) {
            // set the owning side to null (unless already changed)
            if ($collect->getCategorieCollect() === $this) {
                $collect->setCategorieCollect(null);
            }
        }

        return $this;
    }
}

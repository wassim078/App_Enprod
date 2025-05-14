<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table(name: 'post')]
#[ORM\HasLifecycleCallbacks]
class Post
{

    public function __construct()
    {
        $this->ratings = new ArrayCollection();
        $this->average_rating = 0.0;
    }



    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: CategorieForum::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(name: 'forum_id', referencedColumnName: 'id')]
    private $categorie;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private $user;

    #[Assert\NotBlank(message: 'Title cannot be blank')]
    #[ORM\Column(type: 'string', length: 255)]
    private $title;
    #[Assert\NotBlank(message: 'Content cannot be blank')]
    #[ORM\Column(type: Types::TEXT)]
    private $contenu;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private $created_at;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private $updated_at;

    #[ORM\Column(type: 'decimal', precision: 3, scale: 2, nullable: false, options: ['default' => 0.00])]
    private $average_rating;


    #[ORM\OneToMany(mappedBy: 'post', targetEntity: PostRating::class, cascade: ['persist'])]
    private $ratings;


    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateAverageRating(): void
    {
        $total = 0;
        foreach ($this->ratings as $rating) {
            $total += $rating->getRating();
        }

        $this->average_rating = $this->ratings->count() > 0
            ? round($total / $this->ratings->count(), 2)
            : 0.0;

        // Ensure non-null value
        $this->average_rating = (float) $this->average_rating;
    }











    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * @param mixed $categorie
     */
    public function setCategorie($categorie): void
    {
        $this->categorie = $categorie;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return mixed
     */
    public function getAverageRating(): float
    {
        // Handle null values explicitly
        return (float) ($this->average_rating ?? 0.0);
    }

    /**
     * @param mixed $average_rating
     */
    public function setAverageRating($average_rating): void
    {
        $this->average_rating = $average_rating;
    }


    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTime());
        }
        $this->setUpdatedAt(new \DateTime());
    }


    public function getRatings(): Collection
    {
        return $this->ratings;
    }



    public function addRating(PostRating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setPost($this);
        }
        return $this;
    }


}
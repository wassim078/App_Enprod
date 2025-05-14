<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email',groups: ['Registration','AddUser','ModifyUser'])]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;





    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message:'Should be not blank',groups: ['Registration','EditUser','AddUser','ModifyUser'])]
    #[Assert\Length(min:3,exactMessage:'Should be minimum 3 Characters',groups: ['Registration','EditUser','AddUser','ModifyUser'])]
    private ?string $nom = null;



    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message:'Should be not blank',groups: ['Registration','EditUser','AddUser','ModifyUser'])]
    #[Assert\Length(min:3,exactMessage:'Should be minimum 3 Characters',groups: ['Registration','EditUser','AddUser','ModifyUser'])]
    private ?string $prenom = null;


    #[ORM\Column(length: 180)]


    #[Assert\NotBlank(message:'Should be not blank',groups: ['Registration','AddUser','ModifyUser'])]
    #[Assert\Email(message:'Please provide a valid email',groups: ['Registration','AddUser','ModifyUser'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;


    #[Assert\Regex(
        pattern: "/^(?=.*[A-Za-z])(?=.*\d).+$/",
        message: 'Password must contain both letters and numbers',
        groups: ['Registration','EditUser','AddUser','ModifyUser']
    )]
    #[Assert\Length(min:3, max:15,minMessage:'Password should be at least 3 characters long.',maxMessage:'Password should not exceed 15 characters.',groups: ['Registration','EditUser','AddUser','ModifyUser'])]
    #[Assert\NotBlank(message:'Please Enter a Password',groups: ['Registration','AddUser'])]
    private ?string $plainPassword = null;




    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message:'Should be not blank',groups: ['Registration','EditUser','AddUser','ModifyUser'])]
    #[Assert\Length(min:3,exactMessage:'Should be minimum 3 Characters',groups: ['Registration','EditUser','AddUser','ModifyUser'])]
    private ?string $adresse = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message:'Should be not blank',groups: ['Registration','EditUser','AddUser','ModifyUser'])]
    #[Assert\Regex(
    pattern: '/^[0-9]{8,20}$/',
    message: 'Phone number must contain only digits (8-20)',groups: ['Registration','EditUser','AddUser','ModifyUser']
)]
    
    private ?string $telephone = null;

    

    #[ORM\Column(type:'string',length: 255, nullable: true)]
    private ?string $image = null;

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }
    
    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }
  


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USE
        
        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

}

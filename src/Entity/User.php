<?php
 
namespace App\Entity;
 
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;

 
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
 
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;
 
 
    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;


    #[ORM\Column]
    private ?int $phonenumber = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $isdeleted = null;

 
    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $is_admin = null;
 
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
 
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }
 
    public function getPhonenumber(): ?int
    {
        return $this->phonenumber;
    }

    public function setPhonenumber(int $phonenumber): static
    {
        $this->phonenumber = $phonenumber;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $fullname): static
    { //  yna7i l'espace w yrodou  unique k t7ot caracter yna7ihom  b3athnalou fullname ali hia
        // f controller  $fullname=$firstname.$lastname; ya3ni ken t7otlo safa.bo yemchi yna7i lespace yrodha safabo 
        
       
        $slugify = new Slugify();
        $slug = $slugify->slugify($fullname );
        $this->slug = $slug;
        return $this;
    }

    public function getIsdeleted(): ?\DateTimeInterface
    {
        return $this->isdeleted;
    }

    public function setIsdeleted(?\DateTimeInterface $isdeleted): static
    {
        $this->isdeleted = $isdeleted;

        return $this;
    }

   
    public function getUserIdentifier(): string
    {
        return (string) $this->firstname . " " . $this->lastname;
    }

    
 
   
    public function getPassword(): string
    {
        return $this->password;
    }
 
    public function setPassword(string $password): static
    {
        $this->password = $password;
 
        return $this;
    }
 

}
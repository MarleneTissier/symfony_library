<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AuthorRepository::class)
 */
class Author
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Merci d'entrer le prénom de l'auteur !")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Merci d'entrer le nom de l'auteur !")
     */
    private $lastname;

    /**
     * @ORM\Column(type="text", length=500)
     * @Assert\NotBlank(message = "Merci de parler un peu de sa vie !")
     */
    private $biography;

    /**
     * @ORM\Column(type="boolean")
     */
    private $published;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message = "Merci d'indiquer sa date de naissance!")
     */
    private $birthdate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $deathdate;

      /**
      * @ORM\OneToMany(targetEntity=Book::class, mappedBy="author")
      */
    private $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * @param mixed $biography
     */
    public function setBiography($biography): void
    {
        $this->biography = $biography;
    }

    /**
     * @return mixed
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param mixed $published
     */
    public function setPublished($published): void
    {
        $this->published = $published;
    }

    /**
     * @return mixed
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * @param mixed $birthdate
     */
    public function setBirthdate($birthdate): void
    {
        $this->birthdate = $birthdate;
    }

    /**
     * @return mixed
     */
    public function getDeathdate()
    {
        return $this->deathdate;
    }

    /**
     * @param mixed $deathdate
     */
    public function setDeathdate($deathdate): void
    {
        $this->deathdate = $deathdate;
    }


    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setAuthor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
            // set the owning side to null (unless already changed)
            if ($book->getAuthor() === $this) {
                $book->setAuthor(null);
            }
        }

        return $this;
    }



}

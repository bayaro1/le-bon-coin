<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SearchFilterRepository;

#[ORM\Entity(repositoryClass: SearchFilterRepository::class)]
class SearchFilter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    private $category;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $qSearch;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $city;

    /**
     * @var User
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getQSearch(): ?string
    {
        return $this->qSearch;
    }

    public function setQSearch(string $qSearch): self
    {
        $this->qSearch = $qSearch;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get the value of user
     *
     * @return  User
     */ 
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @param  User  $user
     *
     * @return  self
     */ 
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }
}

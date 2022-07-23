<?php

namespace App\Entity;

use App\Repository\SearchFilterRepository;
use Doctrine\ORM\Mapping as ORM;

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
}

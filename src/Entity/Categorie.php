<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomCategorie = null;

    #[ORM\OneToMany(mappedBy: 'Categorie', targetEntity: Article::class)]
    private Collection $article;

    // public function __construct()
    // {
    //     $this->article = new ArrayCollection();
    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCategorie(): ?string
    {
        return $this->nomCategorie;
    }

    public function setNomCategorie(?string $nomCategorie): self
    {
        $this->nomCategorie = $nomCategorie;

        return $this;
    }

    // /**
    //  * @return Collection<int, Article>
    //  */
    // public function getArticle(): Collection
    // {
    //     return $this->article;
    // }

    // public function addArticle(Article $article): self
    // {
    //     if (!$this->article->contains($article)) {
    //         $this->article->add($article);
    //         $article->setCategorie($this);
    //     }

    //     return $this;
    // }

    // public function removeArticle(Article $article): self
    // {
    //     if ($this->article->removeElement($article)) {
    //         //  set the owning side to null (unless already changed)
    //         if ($article->getCategorie() === $this) {
    //             $article->setCategorie(null);
    //         }
    //     }

    //     return $this;
    // }
}

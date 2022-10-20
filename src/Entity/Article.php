<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $nomArticle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageArticle = null;

    #[ORM\Column(nullable: true)]
    private ?float $prixArticle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descriptionArticle = null;

    #[ORM\ManyToOne(inversedBy: 'article')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $Categorie = null;

    // #[ORM\Column(length: 255)]
    // private ?string $type = null;

    // #[ORM\Column(length: 255)]
    // private ?string $vidArticle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomArticle(): ?string
    {
        return $this->nomArticle;
    }

    public function setNomArticle(?string $nomArticle): self
    {
        $this->nomArticle = $nomArticle;

        return $this;
    }

    public function getImageArticle(): ?string
    {
        return $this->imageArticle;
    }

    public function setImageArticle(?string $imageArticle): self
    {
        $this->imageArticle = $imageArticle;

        return $this;
    }

    public function getPrixArticle(): ?float
    {
        return $this->prixArticle;
    }

    public function setPrixArticle(?float $prixArticle): self
    {
        $this->prixArticle = $prixArticle;

        return $this;
    }

    public function getDescriptionArticle(): ?string
    {
        return $this->descriptionArticle;
    }

    public function setDescriptionArticle(?string $descriptionArticle): self
    {
        $this->descriptionArticle = $descriptionArticle;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->Categorie;
    }

    public function setCategorie(?Categorie $Categorie): self
    {
        $this->Categorie = $Categorie;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    // ajout propriété de type string qui contient le nom du fichier que je vais uploader $brochureFilename
    // #[ORM\Column(type: 'string')]
    private $brochureFilename;

    public function getBrochureFilename()
    {
        return $this->brochureFilename;
    }

    public function setBrochureFilename($brochureFilename)
    {
        $this->brochureFilename = $brochureFilename;

        return $this;
    }
}

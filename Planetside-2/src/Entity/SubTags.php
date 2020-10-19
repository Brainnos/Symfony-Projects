<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubTagsRepository")
 */
class SubTags
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sous_categorie;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $icone;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $classes = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Articles", mappedBy="subTag")
     */
    private $articles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vehicules", mappedBy="sub_tag")
     */
    private $vehicules;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Armes", mappedBy="sub_tag_arme")
     */
    private $armes;



    
    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->vehicules = new ArrayCollection();
        $this->armes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSousCategorie(): ?string
    {
        return $this->sous_categorie;
    }

    public function setSousCategorie(string $sous_categorie): self
    {
        $this->sous_categorie = $sous_categorie;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }


    public function getIcone(): ?string
    {
        return $this->icone;
    }

    public function setIcone(?string $icone): self
    {
        $this->icone = $icone;

        return $this;
    }

    public function getClasses(): ?array
    {
        return $this->classes;
    }

    public function setClasses(?array $classes): self
    {
        $this->classes = $classes;

        return $this;
    }

    /**
     * @return Collection|Articles[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Articles $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setSubTag($this);
        }

        return $this;
    }

    public function removeArticle(Articles $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getSubTag() === $this) {
                $article->setSubTag(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Vehicules[]
     */
    public function getVehicules(): Collection
    {
        return $this->vehicules;
    }

    public function addVehicule(Vehicules $vehicule): self
    {
        if (!$this->vehicules->contains($vehicule)) {
            $this->vehicules[] = $vehicule;
            $vehicule->setSubTag($this);
        }

        return $this;
    }

    public function removeVehicule(Vehicules $vehicule): self
    {
        if ($this->vehicules->contains($vehicule)) {
            $this->vehicules->removeElement($vehicule);
            // set the owning side to null (unless already changed)
            if ($vehicule->getSubTag() === $this) {
                $vehicule->setSubTag(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Armes[]
     */
    public function getArmes(): Collection
    {
        return $this->armes;
    }

    public function addArme(Armes $arme): self
    {
        if (!$this->armes->contains($arme)) {
            $this->armes[] = $arme;
            $arme->setSubTagArme($this);
        }

        return $this;
    }

    public function removeArme(Armes $arme): self
    {
        if ($this->armes->contains($arme)) {
            $this->armes->removeElement($arme);
            // set the owning side to null (unless already changed)
            if ($arme->getSubTagArme() === $this) {
                $arme->setSubTagArme(null);
            }
        }

        return $this;
    }

}

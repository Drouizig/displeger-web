<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VerbLocalizationRepository")
 * @ORM\Table(name="VerbLocalization",indexes={@ORM\Index(name="verb_loc_idx", columns={"infinitive"})})
 */
class VerbLocalization
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Verb
     * @ORM\ManyToOne(targetEntity="App\Entity\Verb", inversedBy="localizations")
     * @ORM\JoinColumn(nullable=true)
     */
    private $verb;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $infinitive;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pronunciation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $base;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $dialectCode;

    /**
     * Many Tranlations have Many Sources.
     * @ORM\ManyToMany(targetEntity="Source", cascade={"persist"})
     * @ORM\JoinTable(name="verb_localization_sources",
     *      joinColumns={@ORM\JoinColumn(name="verb_localization_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="source_id", referencedColumnName="id")}
     *      )
     */
    private $sources;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category;

    /**
     * @ORM\Column(type="boolean", length=255, nullable=true)
     */
    private $gouMutation;

    /**
     * @ORM\OneToMany(targetEntity=DescriptionTranslation::class, mappedBy="verbLocalization")
     */
    private $descriptionTranslations;


    public function __construct()
    {
        $this->sources = new ArrayCollection();
        $this->descriptionTranslations = new ArrayCollection();
    }    

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of infinitive
     */ 
    public function getInfinitive()
    {
        return $this->infinitive;
    }

    /**
     * Set the value of infinitive
     *
     * @return  self
     */ 
    public function setInfinitive($infinitive)
    {
        $this->infinitive = $infinitive;

        return $this;
    }

    /**
     * Get the value of pronunciation
     */ 
    public function getPronunciation()
    {
        return $this->pronunciation;
    }

    /**
     * Set the value of pronunciation
     *
     * @return  self
     */ 
    public function setPronunciation($pronunciation)
    {
        $this->pronunciation = $pronunciation;

        return $this;
    }

    /**
     * Get the value of base
     */ 
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Set the value of base
     *
     * @return  self
     */ 
    public function setBase($base)
    {
        $this->base = $base;

        return $this;
    }

    /**
     * Get the value of dialectCode
     */ 
    public function getDialectCode()
    {
        return $this->dialectCode;
    }
    /**
     * Set the value of dialectCode
     */ 
    public function setDialectCode($dialectCode)
    {
        $this->dialectCode = $dialectCode;

        return $this;
    }

    /**
     * Get many Tranlations have Many Sources.
     */ 
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * Set many Tranlations have Many Sources.
     *
     * @return  self
     */ 
    public function setSources($sources)
    {
        $this->sources = $sources;

        return $this;
    }

    /**
     * Add source.
     *
     * @return  self
     */ 
    public function addSource(Source $source)
    {
        $this->sources->add($source);

        return $this;
    }
    /**
     * Remove source.
     *
     * @return  self
     */ 
    public function removeSource(Source $source)
    {
        $this->sources->removeElement($source);

        return $this;
    }
    /**
     * Has source.
     *
     * @return  self
     */ 
    public function hasSource(Source $source)
    {
        return $this->sources->contains($source);
    }

    /**
     * Get the value of verb
     *
     * @return  Verb
     */ 
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * Set the value of verb
     *
     * @param  Verb  $verb
     *
     * @return  self
     */ 
    public function setVerb(?Verb $verb)
    {
        $this->verb = $verb;

        return $this;
    }

    /**
     * Get the value of category
     */ 
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set the value of category
     *
     * @return  self
     */ 
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the value of gouMutation
     */ 
    public function getGouMutation()
    {
        return $this->gouMutation;
    }

    /**
     * Set the value of gouMutation
     *
     * @return  self
     */ 
    public function setGouMutation($gouMutation)
    {
        $this->gouMutation = $gouMutation;

        return $this;
    }

    public function __toString() {
        return $this->infinitive;
    }

    /**
     * @return Collection|DescriptionTranslation[]
     */
    public function getDescriptionTranslations(): Collection
    {
        return $this->descriptionTranslations;
    }

    public function addDescriptionTranslation(DescriptionTranslation $descriptionTranslation): self
    {
        if (!$this->descriptionTranslations->contains($descriptionTranslation)) {
            $this->descriptionTranslations[] = $descriptionTranslation;
            $descriptionTranslation->setVerbLocalization($this);
        }

        return $this;
    }

    public function removeDescriptionTranslation(DescriptionTranslation $descriptionTranslation): self
    {
        if ($this->descriptionTranslations->contains($descriptionTranslation)) {
            $this->descriptionTranslations->removeElement($descriptionTranslation);
            // set the owning side to null (unless already changed)
            if ($descriptionTranslation->getVerbLocalization() === $this) {
                $descriptionTranslation->setVerbLocalization(null);
            }
        }

        return $this;
    }
}

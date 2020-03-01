<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VerbRepository")
 * @ORM\Table(name="Verb")
 */
class Verb
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
    private $category;

    /**
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\VerbTranslation",
     *      mappedBy="verb",
     *      cascade={"all"},
     *      orphanRemoval=true
     * )
     */
    private $translations;

    /**
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\VerbLocalization",
     *      mappedBy="verb",
     *      cascade={"all"},
     *      orphanRemoval=true
     * )
     */
    private $localizations;

    /**
     * Many Verbs have many Verbs.
     * @ORM\ManyToMany(targetEntity="Verb")
     * @ORM\JoinTable(name="auxilliaries",
     *      joinColumns={@ORM\JoinColumn(name="auxilliary_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="verb_id", referencedColumnName="id")}
     *      )
     */
    private $auxilliaries;


    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->localizations = new ArrayCollection();
        $this->auxilliaries = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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
     * Get many Verbs have many Verbs.
     */ 
    public function getAuxilliaries()
    {
        return $this->auxilliaries;
    }

    /**
     * Set many Verbs have many Verbs.
     *
     * @return  self
     */ 
    public function setAuxilliaries($auxilliaries)
    {
        $this->auxilliaries = $auxilliaries;

        return $this;
    }
    /**
     * Add auxilliary.
     *
     * @return  self
     */ 
    public function addAuxilliary(Verb $auxilliary)
    {
        $this->auxilliaries->add($auxilliary);

        return $this;
    }
    /**
     * Remove auxilliary.
     *
     * @return  self
     */ 
    public function removeAuxilliary(Verb $auxilliary)
    {
        $this->auxilliaries->remove($auxilliary);

        return $this;
    }

    /**
     * Get targetEntity="App\Entity\VerbLocalization",
     */ 
    public function getLocalizations()
    {
        return $this->localizations;
    }

    /**
     * Set targetEntity="App\Entity\VerbLocalization",
     *
     * @return  self
     */ 
    public function setLocalizations($localizations)
    {
        $this->localizations = $localizations;

        return $this;
    }
    
    /**
     * Add localization.
     *
     * @return  self
     */ 
    public function addLocalization(VerbLocalization $localization)
    {
        $this->localizations->add($localization);
        $localization->setVerb($this);

        return $this;
    }
    /**
     * Remove localization.
     *
     * @return  self
     */ 
    public function removeLocalization(VerbLocalization $localization)
    {
        $this->localizations->remove($localization);

        return $this;
    }

    /**
     * Get targetEntity="App\Entity\VerbTranslation",
     */ 
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Set targetEntity="App\Entity\VerbTranslation",
     *
     * @return  self
     */ 
    public function setTranslations($translations)
    {
        $this->translations = $translations;

        return $this;
    }
    /**
     * Add translation.
     *
     * @return  self
     */ 
    public function addTranslation(VerbTranslation $translation)
    {
        $this->translations->add($translation);
        $translation->setVerb($this);

        return $this;
    }
    /**
     * Remove tranlsation.
     *
     * @return  self
     */ 
    public function removeTranslation(VerbTranslation $translation)
    {
        $this->translations->remove($translation);

        return $this;
    }

    public function hasTranslationInLanguage(string $languageCode) {
        /** @var VerbTranslation $translation */
        foreach($this->translations as $translation) {
            if($translation->getLanguageCode() === $languageCode) {
                return true;
            }
        }
        return false;
    }
}

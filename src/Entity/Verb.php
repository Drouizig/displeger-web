<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VerbRepository")
 * @ORM\HasLifecycleCallbacks()
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

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\VerbTag",
     *     mappedBy="verb",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     *     )
     */
    private $tags;


    /**
     * @ORM\OneToMany(targetEntity=DescriptionTranslation::class, 
     *      mappedBy="verb",
     *      cascade={"all"},
     *      orphanRemoval=true
     * )
     */
    private $descriptionTranslations;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    #[ORM\Column(nullable: true)]
    private ?bool $wiktionnaryExists = null;

    #[ORM\Column(nullable: true)]
    private ?bool $wiktionnaryConjugationExists = null;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->localizations = new ArrayCollection();
        $this->auxilliaries = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->descriptionTranslations = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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
        $this->auxilliaries->removeElement($auxilliary);

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
        $this->localizations->removeElement($localization);

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


    public function isEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

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
        $this->translations->removeElement($translation);

        return $this;
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
            $descriptionTranslation->setVerb($this);
        }

        return $this;
    }

    public function removeDescriptionTranslation(DescriptionTranslation $descriptionTranslation): self
    {
        if ($this->descriptionTranslations->contains($descriptionTranslation)) {
            $this->descriptionTranslations->removeElement($descriptionTranslation);
            // set the owning side to null (unless already changed)
            if ($descriptionTranslation->getVerb() === $this) {
                $descriptionTranslation->setVerb(null);
            }
        }

        return $this;
    }


    public function getDescription(string $languageCode) 
    {

        /** @var descriptionTranslations $description */
        foreach($this->descriptionTranslations as $description) {
            if($description->getLanguageCode() === $languageCode) {
                return $description;
            }
        }
        $languageCode = explode('_', $languageCode)[0];
        foreach($this->descriptionTranslations as $description) {
            if(explode('_', $description->getLanguageCode())[0] === $languageCode) {
                return $description;
            }
        }

        return null;
    }

    public function hasDescriptionInLanguage(string $languageCode) 
    {
        return $this->getDescription($languageCode) !== null;
    }

    public function hasTranslationInLanguage(string $languageCode) 
    {
        return $this->getTranslation($languageCode) !== null;
    }

    public function getTranslation(string $languageCode) 
    {
        /** @var VerbTranslation $translation */
        foreach($this->translations as $translation) {
            if($translation->getLanguageCode() === $languageCode) {
                return $translation;
            }
        }
        $languageCode = explode('_', $languageCode)[0];
        foreach($this->translations as $translation) {
            if(explode('_', $translation->getLanguageCode())[0] === $languageCode) {
                return $translation;
            }
        }

        return null;
    }

    /**
     * @return Collection|VerbTag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }
    
    public function addTag(VerbTag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $tag->setVerb($this);
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(VerbTag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $tag->setVerb(null);
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    /**
     * Has tag.
     *
     * @return  self
     */ 
    public function hasTag(Tag $tag)
    {
        /** @var VerbTag $verbTag */
        foreach($this->tags as $verbTag) {
            if($verbTag->getTag()->getId() === $tag->getId()) {
                return true;
            }
        }
        return false;
    }
    /**
     * Get Verb tag.
     *
     * @return  self
     */ 
    public function getVerbTag(Tag $tag)
    {
        /** @var VerbTag $verbTag */
        foreach($this->tags as $verbTag) {
            if($verbTag->getTag()->getId() === $tag->getId()) {
                return $verbTag;
            }
        }
        return false;
    }

    public function getWiktionnaryExists(): ?bool
    {
        return $this->wiktionnaryExists;
    }

    public function setWiktionnaryExists(?bool $wiktionnaryExists): void
    {
        $this->wiktionnaryExists = $wiktionnaryExists;
    }

    public function getWiktionnaryConjugationExists(): ?bool
    {
        return $this->wiktionnaryConjugationExists;
    }

    public function setWiktionnaryConjugationExists(?bool $wiktionnaryConjugationExists): void
    {
        $this->wiktionnaryConjugationExists = $wiktionnaryConjugationExists;
    }


}

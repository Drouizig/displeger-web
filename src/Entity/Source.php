<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SourceRepository")
 * @ORM\Table(name="Source")
 */
class Source
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $code;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $locale;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var bool
     * @ORM\Column(type="boolean", length=255, nullable=true)
     */
    private $active;

    /**
     * @var Collection<SourceTranslation>
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\SourceTranslation",
     *      mappedBy="source",
     *      cascade={"all"},
     *      orphanRemoval=true
     * )
     */
    private $translations;


    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->active = false;
    }

    /**
     * Get the value of code
     *
     * @return  string
     */ 
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set the value of code
     *
     * @param  string  $code
     *
     * @return  self
     */ 
    public function setCode(string $code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get targetEntity="App\Entity\SourceTranslation",
     *
     * @return  array<SourceTranslation>
     */ 
    public function getTranslations()
    {
        return $this->translations;
    }
    
    /**
     * Add a translation
     * @param SourceTranslation $translation 
     *
     * @return  self
     */ 
    public function addTranslation(SourceTranslation $translation)
    {
        $this->translations->add($translation);
        $translation->setSource($this);
        
        return $this;
    }

    /**
     * Add a translation
     * @param SourceTranslation $translation 
     *
     * @return  self
     */ 
    public function removeTranslation(SourceTranslation $translation)
    {
        $this->translations->removeElement($translation);
        
        return $this;
    }
    

    public function hasTranslationInLanguage(string $languageCode) {
        return $this->getTranslation($languageCode) !== null;
    }
    
    public function getTranslation(string $languageCode) {
        /** @var SourceTranslation $translation */
        foreach($this->translations as $translation) {
            if($translation->getLanguageCode() === $languageCode) {
                return $translation;
            }
        }
        return null;
    }

    
    public function getNearestTranslation(string $languageCode) {
        /** @var SourceTranslation $translation */
        foreach($this->translations as $translation) {
            if($translation->getLanguageCode() === $languageCode) {
                return $translation;
            }
        }
        return $this->translations->first();
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    public function __toString() {
        return $this->code;
    }

    /**
     * Get the value of type
     *
     * @return  string
     */ 
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param  string  $type
     *
     * @return  self
     */ 
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of locale
     *
     * @return  string
     */ 
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the value of locale
     *
     * @param  string  $locale
     *
     * @return  self
     */ 
    public function setLocale(string $locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get the value of url
     *
     * @return  string
     */ 
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the value of url
     *
     * @param  string  $url
     *
     * @return  self
     */ 
    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the value of active
     *
     * @return  bool
     */ 
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set the value of active
     *
     * @param  bool  $active
     *
     * @return  self
     */ 
    public function setActive(string $active)
    {
        $this->active = $active;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="VerbTranslation")
 */
class VerbTranslation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Verb
     * @ORM\ManyToOne(targetEntity="App\Entity\Verb")
     * @ORM\JoinColumn(nullable=true)
     */
    private $verb;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $translation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $languageCode;

    /**
     * Many Tranlations have Many Sources.
     * @ORM\ManyToMany(targetEntity="Source")
     * @ORM\JoinTable(name="verb_translation_sources",
     *      joinColumns={@ORM\JoinColumn(name="verb_translation_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="source_id", referencedColumnName="id")}
     *      )
     */
    private $sources;

    public function __construct()
    {
        $this->sources = new ArrayCollection();
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
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
    public function setVerb(Verb $verb)
    {
        $this->verb = $verb;

        return $this;
    }

    /**
     * Get the value of languageCode
     */ 
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     * Set the value of languageCode
     *
     * @return  self
     */ 
    public function setLanguageCode($languageCode)
    {
        $this->languageCode = $languageCode;

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
       $this->sources->remove($source);

       return $this;
   }

    /**
     * Get the value of translation
     */ 
    public function getTranslation()
    {
        return $this->translation;
    }

    /**
     * Set the value of translation
     *
     * @return  self
     */ 
    public function setTranslation($translation)
    {
        $this->translation = $translation;

        return $this;
    }
}

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
    
    public function getTranslation(string $languageCode) {
        /** @var SourceTranslation $translation */
        foreach($this->translations as $translation) {
            if($translation->getLanguageCode() === $languageCode) {
                return $translation;
            }
        }
        return null;
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
}

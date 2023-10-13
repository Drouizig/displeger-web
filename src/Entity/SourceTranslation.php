<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="SourceTranslation")
 */
class SourceTranslation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Source
     * @ORM\ManyToOne(targetEntity="App\Entity\Source", inversedBy="translations")
     * @ORM\JoinColumn(nullable=true)
     */
    private $source;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $languageCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of label
     */ 
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the value of label
     *
     * @return  self
     */ 
    public function setLabel($label)
    {
        $this->label = $label;

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
     * Get the value of source
     *
     * @return  Source
     */ 
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the value of source
     *
     * @param  Source  $source
     *
     * @return  self
     */ 
    public function setSource(Source $source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return '['.strtoupper($this->languageCode).'] '.$this->label;
    }


}

<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConfigurationRepository")
 */
class Configuration
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
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\ConfigurationTranslation",
     *      mappedBy="configuration",
     *      cascade={"all"},
     *      orphanRemoval=true
     * )
     */
    private $translations;

    /**
     * Configuration constructor.
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTranslations()
    {
        return $this->translations;
    }
    /**
     * @return ConfigurationTranslation
     */
    public function getTranslation($locale = 'br')
    {
        /** @var ConfigurationTranslation $translation */
        foreach($this->translations as $translation) {
            if($translation->getLocale() === $locale) {
                return $translation;
            }
        }

        return null;
    }

    /**
     * @param Collection $translations
     */
    public function setTranslations($translations): void
    {
        $this->translations = $translations;
    }

    /**
     * Add translation.
     *
     * @return  self
     */ 
    public function addTranslation(ConfigurationTranslation $translation)
    {
        $this->translations->add($translation);
        $translation->setConfiguration($this);

        return $this;
    }
    /**
     * Remove tranlsation.
     *
     * @return  self
     */ 
    public function removeTranslation(ConfigurationTranslation $translation)
    {
        $this->translations->removeElement($translation);

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code)
    {
        $this->code = $code;
    }
}

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
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\ConfigurationTranslation",
     *      mappedBy="configuration",
     *      cascade={"all"},
     *      orphanRemoval=true
     * )
     */
    private $translations;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $intro;

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

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setIntro(?string $intro): self
    {
        $this->intro = $intro;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param Collection $translations
     */
    public function setTranslations($translations): void
    {
        $this->translations = $translations;
    }
}

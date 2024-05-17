<?php

namespace App\Entity;

use App\Repository\DescriptionTranslationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DescriptionTranslationRepository::class)
 */
class DescriptionTranslation implements \Stringable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $languageCode;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=Verb::class)
     */
    private $verb;

    /**
     * @ORM\ManyToOne(targetEntity=VerbLocalization::class, inversedBy="descriptionTranslations")
     */
    private $verbLocalization;

    /**
     * @ORM\ManyToMany(targetEntity=Source::class)
     */
    private $sources;

    public function __construct()
    {
        $this->sources = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(?string $languageCode): self
    {
        $this->languageCode = $languageCode;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getVerb(): ?Verb
    {
        return $this->verb;
    }

    public function setVerb(?Verb $verb): self
    {
        $this->verb = $verb;

        return $this;
    }

    public function getVerbLocalization(): ?VerbLocalization
    {
        return $this->verbLocalization;
    }

    public function setVerbLocalization(?VerbLocalization $verbLocalization): self
    {
        $this->verbLocalization = $verbLocalization;

        return $this;
    }

    /**
     * @return Collection|Source[]
     */
    public function getSources(): Collection
    {
        return $this->sources;
    }

    public function addSource(Source $source): self
    {
        if (!$this->sources->contains($source)) {
            $this->sources[] = $source;
        }

        return $this;
    }

    public function removeSource(Source $source): self
    {
        if ($this->sources->contains($source)) {
            $this->sources->removeElement($source);
        }

        return $this;
    }

    public function __toString()
    {
        return sprintf('[%s] %s', $this->getLanguageCode(), $this->getContent());
    }
}

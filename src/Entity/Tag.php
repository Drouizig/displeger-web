<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 * @ORM\Table(name="Tag")
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity=TagTranslation::class,
     *     mappedBy="tag",
     *     cascade={"all"},
     *     orphanRemoval=true
     *     )
     */
    private $translations;

    /**
     * @ORM\ManyToMany(targetEntity=Verb::class, mappedBy="tags")
     */
    private $verbs;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->verbs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString() {
        return $this->code;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection|TagTranslation[]
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(TagTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setTag($this);
        }

        return $this;
    }

    public function removeTranslation(TagTranslation $translation): self
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);
            // set the owning side to null (unless already changed)
            if ($translation->getTag() === $this) {
                $translation->setTag(null);
            }
        }

        return $this;
    }

    public function getTranslation(string $languageCode) {
        /** @var TagTranslation $translation */
        foreach($this->translations as $translation) {
            if($translation->getLanguageCode() === $languageCode) {
                return $translation;
            }
        }
        return null;
    }

    /**
     * @return Collection|Verb[]
     */
    public function getVerbs(): Collection
    {
        return $this->verbs;
    }

    public function getVerb(string $verbId) {
        /** @var Verb $verb */
        foreach($this->verbs as $verb) {
            if($verb->getId() === $verbId) {
                return $verb;
            }
        }
        return null;
    }

    public function addVerb(Verb $verb): self
    {
        if (!$this->verbs->contains($verb)) {
            $this->verbs[] = $verb;
            $verb->addTag($this);
        }

        return $this;
    }

    public function removeVerb(Verb $verb): self
    {
        if ($this->verbs->contains($verb)) {
            $this->verbs->removeElement($verb);
            $verb->removeTag($this);
        }

        return $this;
    }
}

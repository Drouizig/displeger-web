<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
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
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity=TagTranslation::class, mappedBy="tag")
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

    /**
     * @return Collection|Verb[]
     */
    public function getVerbs(): Collection
    {
        return $this->verbs;
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

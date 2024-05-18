<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagCategoryRepository")
 * @ORM\Table(name="TagCategory")
 */
class TagCategory
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
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $color;

    /**
     * @ORM\OneToMany(targetEntity=Tag::class, mappedBy="category")
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity=TagCategoryTranslation::class,
     *     mappedBy="tagCategory",
     *     cascade={"all"},
     *     orphanRemoval=true
     *     )
     */
    private $translations;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->translations = new ArrayCollection();
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
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->setCategory($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
            // set the owning side to null (unless already changed)
            if ($tag->getCategory() === $this) {
                $tag->setCategory(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection|TagCategoryTranslation[]
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function addTranslation(TagCategoryTranslation $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setTagCategory($this);
        }

        return $this;
    }

    public function removeTranslation(TagCategoryTranslation $translation): self
    {
        if ($this->translations->contains($translation)) {
            $this->translations->removeElement($translation);
            // set the owning side to null (unless already changed)
            if ($translation->getTagCategory() === $this) {
                $translation->setTagCategory(null);
            }
        }

        return $this;
    }

    public function getTranslation(string $languageCode) {
        /** @var TagCategoryTranslation $translation */
        foreach($this->translations as $translation) {
            if($translation->getLanguageCode() === $languageCode) {
                return $translation;
            }
        }
        return null;
    }


    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }
}

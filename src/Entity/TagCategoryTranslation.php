<?php

namespace App\Entity;

use App\Repository\TagTranslationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TagCategoryTranslationRepository::class)
 */
class TagCategoryTranslation
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
    private $label;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $languageCode;

    /**
     * @ORM\ManyToOne(targetEntity=TagCategory::class, inversedBy="translations")
     */
    private $tagCategory;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(string $languageCode): self
    {
        $this->languageCode = $languageCode;

        return $this;
    }

    public function getTagCategory(): ?TagCategory
    {
        return $this->tagCategory;
    }

    public function setTagCategory(?TagCategory $tagCategory): self
    {
        $this->tagCategory = $tagCategory;

        return $this;
    }

    public function __toString()
    {
        return '['.$this->getLanguageCode().'] '.
            $this->getLabel();
    }
}

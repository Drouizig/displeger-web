<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VerbRepository")
 * @ORM\Table(name="Verb",indexes={@ORM\Index(name="verb_idx", columns={"anv_verb"})})
 */
class Verb
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
    private $anvVerb;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pennrann;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $galleg;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $saozneg;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnvVerb(): ?string
    {
        return $this->anvVerb;
    }

    public function setAnvVerb(string $anvVerb): self
    {
        $this->anvVerb = $anvVerb;

        return $this;
    }

    public function getPennrann(): ?string
    {
        return $this->pennrann;
    }

    public function setPennrann(string $pennrann): self
    {
        $this->pennrann = $pennrann;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getGalleg(): ?string
    {
        return $this->galleg;
    }

    public function setGalleg(?string $galleg): self
    {
        $this->galleg = $galleg;

        return $this;
    }

    public function getSaozneg(): ?string
    {
        return $this->saozneg;
    }

    public function setSaozneg(?string $saozneg): self
    {
        $this->saozneg = $saozneg;

        return $this;
    }
}

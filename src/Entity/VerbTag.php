<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VerbTagRepository")
 * @ORM\Table(name="verb_tag")
 */
class VerbTag
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Tag",
     *     inversedBy="verbs"
     *     )
     * @ORM\JoinColumn(nullable=true)
     */
    private $tag;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Verb", inversedBy="tags")
     * @ORM\JoinColumn(nullable=true)
     */
    private $verb;

    /**
     * Many tags have Many Sources.
     * @ORM\ManyToMany(targetEntity="Source")
     * @ORM\JoinTable(name="verb_tag_sources",
     *      joinColumns={
     *          @ORM\JoinColumn(name="verb_tag_verb_id", referencedColumnName="verb_id"),
     *          @ORM\JoinColumn(name="verb_tag_tag_id", referencedColumnName="tag_id")
     *       },
     *      inverseJoinColumns={@ORM\JoinColumn(name="source_id", referencedColumnName="id")}
     *      )
     */
    private $sources;


    public function __construct()
    {
        $this->verbs = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->sources = new ArrayCollection();
    }

    /**
     * @return Tag
     */
    public function getTag(): Tag
    {
        return $this->tag;
    }

    /**
     */
    public function setTag(?Tag $tag): void
    {
        $this->tag = $tag;
    }


    
    /**
     * @return Verb
     */
    public function getVerb(): Verb
    {
        return $this->verb;
    }
    /**
     */
    public function setVerb(?Verb $verb): void
    {
        $this->verb = $verb;
    }

    /**
     * Get many Tranlations have Many Sources.
     */ 
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * Set many Tranlations have Many Sources.
     *
     * @return  self
     */ 
    public function setSources($sources)
    {
        $this->sources = $sources;

        return $this;
    }    
    
   /**
    * Add source.
    *
    * @return  self
    */ 
   public function addSource(Source $source)
   {
       $this->sources->add($source);

       return $this;
   }
   /**
    * Remove source.
    *
    * @return  self
    */ 
   public function removeSource(Source $source)
   {
       $this->sources->remove($source);

       return $this;
   }


}

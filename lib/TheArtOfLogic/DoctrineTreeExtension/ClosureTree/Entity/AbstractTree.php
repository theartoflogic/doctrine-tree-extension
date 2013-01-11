<?php

namespace TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractTree
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10, options={"unsigned"=true})
     */
    protected $ancestor;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10, options={"unsigned"=true})
     */
    protected $descendant;

    /**
     * @ORM\Column(type="smallint", length=5, options={"unsigned"=true})
     */
    protected $depth;

    /**
     * Set ancestor
     *
     * @param object $ancestor
     * 
     * @return AbstractTree
     */
    public function setAncestor($ancestor)
    {
        $this->ancestor = $ancestor;

        return $this;
    }

    /**
     * Get ancestor
     *
     * @return object
     */
    public function getAncestor()
    {
        return $this->ancestor;
    }

    /**
     * Set descendant
     *
     * @param object $descendant
     * 
     * @return AbstractTree
     */
    public function setDescendant($descendant)
    {
        $this->descendant = $descendant;

        return $this;
    }

    /**
     * Get descendant
     *
     * @return object
     */
    public function getDescendant()
    {
        return $this->descendant;
    }

    /**
     * Set depth
     *
     * @param integer $depth
     * 
     * @return AbstractTree
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;

        return $this;
    }

    /**
     * Get depth
     *
     * @return integer
     */
    public function getDepth()
    {
        return $this->depth;
    }
}
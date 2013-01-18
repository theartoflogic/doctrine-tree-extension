<?php

namespace TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Entity;

use Doctrine\ORM\Mapping as ORM;
use TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation as ClosureTree;

/**
 * @ORM\MappedSuperclass
 * @ClosureTree\Tree
 */
abstract class AbstractDepthTree
{
    protected $ancestor;
    protected $descendant;

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
     * @ORM\Column(type="smallint", length=5, options={"unsigned"=true})
     * @ClosureTree\Depth
     */
    protected $depth;

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
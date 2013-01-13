<?php

namespace TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Entity;

use Doctrine\ORM\Mapping as ORM;
use TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation as ClosureTree;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractTree
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10, options={"unsigned"=true})
     * @ClosureTree\Ancestor
     */
    protected $ancestor;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", length=10, options={"unsigned"=true})
     * @ClosureTree\Descendant
     */
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
}
<?php

namespace TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Entity;

use Doctrine\ORM\Mapping as ORM;
use TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation as ClosureTree;

/**
 * @ORM\MappedSuperclass
 * @ClosureTree\Tree
 */
abstract class AbstractDepthTree extends AbstractTree
{

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
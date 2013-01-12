<?php

namespace TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Node
{
    /**
     * Specifies the entity to use for the tree data.
     *
     * @var string
     */
    public $treeEntity;
    
    /**
     * Specifies the name of the table to use for the tree data.
     *
     * @var string
     */
    public $treeTable;
    
    /**
     * Specifies whether to use the depth field.
     *
     * @var boolean
     */
    public $withDepth = true;
}
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
    public $entity;
    
    /**
     * Specifies the name of the table to use for the tree data.
     *
     * @var string
     */
    public $table;
    
    /**
     * Specifies the name of the column to use for the ancestor.
     *
     * @var string
     */
    public $ancestorColumn;
    
    /**
     * Specifies the name of the column to use for the descendant.
     *
     * @var string
     */
    public $descendantColumn;
    
    /**
     * Specifies whether to use the depth field.
     *
     * @var boolean
     */
    public $withDepth = true;
}
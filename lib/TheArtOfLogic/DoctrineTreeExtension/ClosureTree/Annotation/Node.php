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
     * Specifies the name of the table to use for the tree data.
     *
     * @var string
     */
    public $treeTable;

    /**
     * Specifies the entity to use for the tree data.
     *
     * @var string
     */
    public $treeEntity;
}
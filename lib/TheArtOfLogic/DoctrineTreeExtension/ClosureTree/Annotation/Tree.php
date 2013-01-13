<?php

namespace TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Tree
{
    /**
     * Specifies the entity to use for the node data.
     *
     * @var string
     */
    public $nodeEntity;
}
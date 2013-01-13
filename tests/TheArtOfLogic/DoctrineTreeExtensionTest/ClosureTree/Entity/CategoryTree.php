<?php

namespace TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity;

use Doctrine\ORM\Mapping as ORM;
use TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation as ClosureTree;
use TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Entity\AbstractTree;

/**
 * @ORM\Entity
 * @ORM\Table("category_tree")
 * @ClosureTree\Tree(nodeEntity="Category")
 */
class CategoryTree extends AbstractTree
{
}
<?php

namespace TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation as ClosureTree;

/**
 * @ORM\Entity(repositoryClass="TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Entity\Repository\EntityRepository")
 * @ORM\Table("category_with_depth_tree")
 * @ClosureTree\Node(treeEntity="CategoryDepthTree")
 */
class CategoryWithDepthTree
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=75)
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="CategoryWithDepthTree", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     * @ClosureTree\NodeParent
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="CategoryWithDepthTree", mappedBy="parent")
     */
    protected $children;
    
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setParent(CategoryWithDepthTree $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function addChild(CategoryWithDepthTree $child)
    {
        $this->children[] = $child;
    
        return $this;
    }

    public function removeChild(CategoryWithDepthTree $child)
    {
        $this->children->removeElement($child);
    }

    public function getChildren()
    {
        return $this->children;
    }
}
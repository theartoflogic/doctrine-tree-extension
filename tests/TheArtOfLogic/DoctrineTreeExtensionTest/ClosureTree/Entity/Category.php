<?php

namespace TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation as ClosureTree;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Entity\Repository\EntityRepository")
 * @ClosureTree\Node
 */
class Category
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
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     * @ClosureTree\NodeParent
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
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

    public function setParent(Category $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function addChild(Category $child)
    {
        $this->children[] = $child;
    
        return $this;
    }

    public function removeChild(Category $child)
    {
        $this->children->removeElement($child);
    }

    public function getChildren()
    {
        return $this->children;
    }
}
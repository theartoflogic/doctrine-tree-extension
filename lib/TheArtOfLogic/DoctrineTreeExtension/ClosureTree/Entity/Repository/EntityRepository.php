<?php

namespace TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Entity\Repository;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;

class EntityRepository extends BaseEntityRepository
{
    /**
     * Find the child nodes for the specified parent node,
     * or find all root nodes if no parent node is specified.
     *
     * @return array Returns an array containing the results.
     */
    public function findNodes($parentId=null)
    {
        return $this->getNodesQueryBuilder($parentId)->getQuery()->getResult();
    }

    /**
     * Find the root nodes.
     *
     * @return array Returns an array containing the results.
     */
    public function findRootNodes()
    {
        return $this->findNodes();
    }

    /**
     * Find the child nodes for the specified parent.
     *
     * @return array Returns an array containing the results.
     */
    public function findChildNodes($parentId)
    {
        return $this->findNodes($parentId);
    }

    /**
     * Find the number of child nodes for the specified parent
     * node, or find the number of root nodes if no parent
     * node is specified.
     *
     * @return int Returns the number of results found.
     */
    public function findNodeCount($parentId=null)
    {
        // Get class metadata
        $metadata = $this->getClassMetadata();

        return $this->getNodesQueryBuilder($parentId)
            ->select('COUNT(node.'. $metadata->getSingleIdentifierColumnName() .')')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @see findNodeCount()
     */
    public function findRootNodeCount()
    {
        return $this->findNodeCount();
    }

    /**
     * @see findNodeCount()
     */
    public function findChildNodeCount($parentId)
    {
        return $this->findNodeCount($parentId);
    }

    /**
     * Get the query builder, pre-populated with the query
     * conditions to select the child nodes for a particular
     * parent (or the root nodes if no parent is specified).
     *
     * @return object Returns an instance of the query builder.
     */
    public function getNodesQueryBuilder($parentId=null)
    {
        // Get class metadata
        $metadata = $this->getClassMetadata();

        // Create the query builder
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder
            ->select('node')
            ->from($metadata->name, 'node');

        // Get the name of the parent field
        $parentField = $metadata->associationMappings['parent']['fieldName'];

        if (!$parentId) {
            $queryBuilder->where('node.'. $parentField .' IS NULL');
        } else {
            $queryBuilder->where('node.'. $parentField .' = :parentId')->setParameter('parentId', $parentId);
        }

        return $queryBuilder;
    }

    /**
     * @see getNodesQueryBuilder()
     */
    public function getRootNodesQueryBuilder()
    {
        return $this->getNodesQueryBuilder();
    }

    /**
     * @see getNodesQueryBuilder()
     */
    public function getChildNodesQueryBuilder($parentId)
    {
        return $this->getNodesQueryBuilder($parentId);
    }

    /**
     * Get the query builder, pre-populated with the query
     * conditions to select the hierarchy of child nodes
     * for the specified parent node.
     *
     * @return object Returns an instance of the query builder.
     */
    public function getChildHierarchyQueryBuilder($parentId=null)
    {
        // Get class metadata
        $meta = $this->getClassMetadata();

        // Create the query builder
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder
            ->select('node')
            ->from($meta->name, 'node')
            ->innerJoin('');

        // Get the name of the parent field
        $parentField = $meta->associationMappings['parent']['fieldName'];

        if (!$parentId) {
            $queryBuilder->where('node.'. $parentField .' IS NULL');
        } else {
            $queryBuilder->where('node.'. $parentField .' = :parentId')
                ->setParameter('parentId', $parentId);
        }

        if ($sortBy) {
            $sortDir = strtolower($sortDir) ?: 'asc';
            $queryBuilder->orderBy($sortBy, $sortDir);
        }

        return $queryBuilder;
    }

    public function findParentHierarchy()
    {
        
    }

    public function findChildHierarchy()
    {
        
    }
}
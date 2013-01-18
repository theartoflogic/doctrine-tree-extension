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
        $parentColumn = $metadata->closureTree['parent']['fieldName'];

        if (!$parentId) {
            $queryBuilder->where('node.'. $parentColumn .' IS NULL');
        } else {
            $queryBuilder->where('node.'. $parentColumn .' = :parentId')->setParameter('parentId', $parentId);
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
    public function getChildHierarchyQueryBuilder($parentId = null, $withParent = false)
    {
        // Get class metadata
        $metadata = $this->getClassMetadata();
        $treeMetadata = $this->_em->getClassMetadata($metadata->closureTree['treeEntity']);

        // Get the column names
        $nodeIdColumn = $metadata->getSingleIdentifierColumnName();
        $ancestorColumn = $treeMetadata->closureTree['ancestor']['fieldName'];
        $descendantColumn = $treeMetadata->closureTree['descendant']['fieldName'];

        // Create the query builder
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder
            ->select('node')
            ->from($metadata->name, 'node')
            ->innerJoin($treeMetadata->name, 'tree', 'WITH', 'node.'. $nodeIdColumn .' = tree.'. $descendantColumn)
            ->where('tree.'. $ancestorColumn .' = :ancestor')
            ->setParameter('ancestor', $parentId);

        // Check if we should exclude the parent itself
        if (!$withParent) {
            $queryBuilder->andWhere('tree.'. $ancestorColumn .' != tree.'. $descendantColumn);
        }

        return $queryBuilder;
    }

    /**
     * Find the child hierarchy for the specified parent node.
     *
     * @param integer $parentId
     *
     * @return array Returns the array of results.
     */
    public function findChildHierarchy($parentId)
    {
        return $this->getChildHierarchyQueryBuilder($parentId)->getQuery()->getResult();
    }

    /**
     * Get the query builder, pre-populated with the query
     * conditions to select the hierarchy of parent nodes
     * for the specified child node.
     *
     * @return object Returns an instance of the query builder.
     */
    public function getParentHierarchyQueryBuilder($childId = null, $withChild = false)
    {
        // Get class metadata
        $metadata = $this->getClassMetadata();
        $treeMetadata = $this->_em->getClassMetadata($metadata->closureTree['treeEntity']);

        // Get the column names
        $nodeIdColumn = $metadata->getSingleIdentifierColumnName();
        $ancestorColumn = $treeMetadata->closureTree['ancestor']['fieldName'];
        $descendantColumn = $treeMetadata->closureTree['descendant']['fieldName'];

        // Create the query builder
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder
            ->select('node')
            ->from($metadata->name, 'node')
            ->innerJoin($treeMetadata->name, 'tree', 'WITH', 'node.'. $nodeIdColumn .' = tree.'. $ancestorColumn)
            ->where('tree.'. $descendantColumn .' = :descendant')
            ->setParameter('descendant', $childId);

        // Check if we should exclude the child itself
        if (!$withChild) {
            $queryBuilder->andWhere('tree.'. $ancestorColumn .' != tree.'. $descendantColumn);
        }

        return $queryBuilder;
    }

    public function findParentHierarchy($childId)
    {
        return $this->getParentHierarchyQueryBuilder($childId)->getQuery()->getResult();
    }
}
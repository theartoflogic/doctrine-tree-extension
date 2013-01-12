<?php

namespace TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Entity\Repository;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;

class EntityRepository extends BaseEntityRepository
{
    /**
     * Get the query builder, pre-populated with the query
     * conditions to select the child nodes for a particular
     * parent (or the root nodes if no parent is specified).
     *
     * @return object Returns an instance of the query builder.
     */
    public function getChildNodesQueryBuilder($parentId=null, $sortBy=null, $sortDir=null)
    {
        // Get class metadata
        $meta = $this->getClassMetadata();

        // Create the query builder
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder
            ->select('node')
            ->from($meta->name, 'node');

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

    /**
     * Finds the child nodes for the specified parent. If no parent
     * is specified then the root nodes are returned.
     *
     * @return array Returns an array containing the results.
     */
    public function findChildNodes($parentId=null, $sortBy=null, $sortDir=null)
    {
        return $this->getChildNodesQueryBuilder($parentId, $sortBy, $sortDir)->getQuery()->getResult();
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
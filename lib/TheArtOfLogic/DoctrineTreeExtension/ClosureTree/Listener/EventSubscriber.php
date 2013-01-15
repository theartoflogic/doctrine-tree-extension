<?php

namespace TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Listener;

use Doctrine\Common\EventArgs;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use TheArtOfLogic\DoctrineTreeExtension\Listener\EventSubscriber as BaseEventSubscriber;
use TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation\Tree;
use TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation\Node;
use TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation\NodeParent;
use TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation\Ancestor;
use TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation\Descendant;
use TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation\Depth;

/**
 * @author Sarah Ryan <sryan@phunware.com>
 */
class EventSubscriber extends BaseEventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata',
            'onFlush',
            'postPersist',
            'preUpdate',
            'postRemove'
        );
    }

    /**
     * Track whether the class is a node.
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        // Get the class metadata
        $metadata = $eventArgs->getClassMetadata();

        // Get the class annotations
        $annotations = $this->reader->getClassAnnotations($metadata->getReflectionClass());

        // Check if the entity has the closure tree annotation
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Node) {

                // Process the node metadata
                $this->processNode($eventArgs, $metadata, $annotation);

                break;
            } elseif ($annotation instanceof Tree) {

                // Process the tree metadata
                $this->processNodeTree($eventArgs, $metadata, $annotation);

                break;
            }
        }
    }

    /**
     * Process the node metadata.
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     * @param ClassMetadata $metadata
     * @param Node $annotation
     */
    protected function processNode(LoadClassMetadataEventArgs $eventArgs, ClassMetadata $metadata, Node $annotation)
    {
        $className = $metadata->name;

        // Holds the closure tree data
        $closureTreeData = array();

        // Check if we should either use the default entity or prepend the node namespace
        if (!$annotation->treeEntity) {
            $closureTreeData['treeEntity'] = $className .'Tree';
        } elseif(strpos($annotation->treeEntity, '\\') === false) {
            $closureTreeData['treeEntity'] = $metadata->namespace .'\\'. $annotation->treeEntity;
        }

        // Loop through the properties and find the parent column
        foreach ($metadata->getAssociationMappings() as $propertyName => $propertyData) {

            $propertyAnnotations = $this->reader->getPropertyAnnotations(new \ReflectionProperty($className, $propertyName));

            // Check if this property has the node parent annotation
            foreach ($propertyAnnotations as $propertyAnnotation) {
                if ($propertyAnnotation instanceof NodeParent) {

                    // Set the parent data
                    $closureTreeData['parent'] = $propertyData;

                    break 2;
                }
            }
        }

        // Make sure a parent was specified
        if (!isset($closureTreeData['parent'])) {
            throw new \Exception('You must define a parent column for the closure tree entity '. $className .'.');
        }

        // Set the closure tree data
        $metadata->closureTree = $closureTreeData;
    }

    /**
     * Process the node tree metadata.
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     * @param ClassMetadata $metadata
     * @param Tree $annotation
     */
    protected function processNodeTree(LoadClassMetadataEventArgs $eventArgs, ClassMetadata $metadata, Tree $annotation)
    {
        $className = $metadata->name;

        // Holds the closure tree data
        $closureTreeData = array();

        // Check if we should either use the default entity or use the tree name without the 'Tree' suffix
        if (!$annotation->nodeEntity) {
            $closureTreeData['nodeEntity'] = substr($className, 0, -4);
        } elseif(strpos($annotation->nodeEntity, '\\') === false) {
            $closureTreeData['nodeEntity'] = $metadata->namespace .'\\'. $annotation->nodeEntity;
        }

        // Initialize the depth parameter
        $closureTreeData['depth'] = false;

        // Loop through the properties and find the parent column
        foreach ($metadata->fieldMappings as $propertyName => $propertyData) {

            $propertyAnnotations = $this->reader->getPropertyAnnotations(new \ReflectionProperty($className, $propertyName));

            // Check if this property has the node parent annotation
            foreach ($propertyAnnotations as $propertyAnnotation) {
                if ($propertyAnnotation instanceof Ancestor) {

                    // Set the parent data
                    $closureTreeData['ancestor'] = $propertyData;

                    break;
                } elseif ($propertyAnnotation instanceof Descendant) {

                    // Set the parent data
                    $closureTreeData['descendant'] = $propertyData;

                    break;
                } elseif ($propertyAnnotation instanceof Depth) {

                    // Set the parent data
                    $closureTreeData['depth'] = $propertyData;

                    break;
                }
            }
        }

        // Set the closure tree data
        $metadata->closureTree = $closureTreeData;
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        $entityManager = $eventArgs->getEntityManager();
        $nodeClass = get_class($entity);

        // Get metadata
        $nodeMetadata = $entityManager->getClassMetadata($nodeClass);

        // Make sure the entity is a tree node
        if (isset($nodeMetadata->closureTree)) {

            // Save the node tree
            $this->saveNodeTree($entityManager, $entity, $nodeMetadata);

        }
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        $entityManager = $eventArgs->getEntityManager();
        $nodeClass = get_class($entity);

        // Get metadata
        $nodeMetadata = $entityManager->getClassMetadata($nodeClass);

        // Make sure the entity is a tree node
        if (isset($nodeMetadata->closureTree)) {

            $parentColumn = $nodeMetadata->closureTree['parent']['fieldName'];

            // Check if the node's parent changed
            if ($eventArgs->hasChangedField($parentColumn)) {

                // Delete the current node tree
                $this->deleteNodeTree($entityManager, $entity, $nodeMetadata);

                // Save the new node tree
                $this->saveNodeTree($entityManager, $entity, $nodeMetadata);
            }

        }
    }

    /**
     * {@inheritdoc}
     */
    public function postRemove(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        $entityManager = $eventArgs->getEntityManager();
        $nodeClass = get_class($entity);

        // Get metadata
        $nodeMetadata = $entityManager->getClassMetadata($nodeClass);

        // Make sure the entity is a tree node
        if (isset($nodeMetadata->closureTree)) {

            // Save the node tree
            $this->deleteNodeTree($entityManager, $entity, $nodeMetadata);

        }
    }

    /**
     * Delete the tree hierarchy for the specified node.
     *
     * @param EntityManager $entityManager
     * @param object $entity The node to delete the tree for.
     * @param ClassMetadata $nodeMetadata
     */
    protected function deleteNodeTree(EntityManager $entityManager, $entity, ClassMetadata $nodeMetadata)
    {
        $treeClass = $nodeMetadata->closureTree['treeEntity'];

        // Get the node details
        $idColumn = $nodeMetadata->getSingleIdentifierFieldName();
        $nodeId = $nodeMetadata->getReflectionProperty($idColumn)->getValue($entity);

        // Delete current tree
        $entityManager->getRepository($treeClass)->createQueryBuilder('t')
            ->delete()
            ->where('t.descendant = ?1')
            ->setParameter(1, $nodeId)
            ->getQuery()
            ->execute();
    }

    /**
     * Save the tree hierarchy for the specified node.
     *
     * @param EntityManager $entityManager
     * @param object $entity The node to save the tree for.
     * @param ClassMetadata $nodeMetadata
     * @param object|null $parent The parent entity (or null to get it from the entity itself).
     */
    protected function saveNodeTree(EntityManager $entityManager, $entity, ClassMetadata $nodeMetadata, $parent=null)
    {
        $nodeClass = get_class($entity);
        $treeClass = $nodeMetadata->closureTree['treeEntity'];

        // Get metadata
        $treeMetadata = $entityManager->getClassMetadata($treeClass);

        // Get the table name
        $tableName = $treeMetadata->getTableName();
        $idColumn = $nodeMetadata->getSingleIdentifierFieldName();
        $ancestorColumn = $treeMetadata->closureTree['ancestor']['fieldName'];
        $descendantColumn = $treeMetadata->closureTree['descendant']['fieldName'];
        if ($treeMetadata->closureTree['depth']) {
            $depthColumn = $treeMetadata->closureTree['depth']['fieldName'];
        } else {
            $depthColumn = null;
        }
        $nodeId = $nodeMetadata->getReflectionProperty($idColumn)->getValue($entity);
        $parent = $parent ?: $entity->getParent();

        // Check if the entity has a parent
        if ($parent) {

            // Get the parent ID
            $parentId = $parent->getId();

            // Format the query to insert the tree hierarchy
            $query = 'INSERT INTO '. $tableName .' ('. $ancestorColumn .', '. $descendantColumn;
            if ($depthColumn) {
                $query .= ', '. $depthColumn;
            }
            $query .= ') SELECT '. $ancestorColumn .', '. $nodeId .' ';
            if ($depthColumn) {
                $query .= ', ('. $depthColumn .' + 1) ';
            }
            $query .= 'FROM '. $tableName .' ';
            $query .= 'WHERE '. $descendantColumn .' = ? ';
            $query .= 'UNION ALL SELECT '. $nodeId .', '. $nodeId;
            if ($depthColumn) {
                $query .= ', 0';
            }

            // Set the query parameters
            $queryParams = array($parentId);

        } else {

            // Format the query to insert the tree hierarchy
            $query = 'INSERT INTO '. $tableName .' ';
            $query .= '('. $ancestorColumn .', '. $descendantColumn;
            if ($depthColumn) {
                $query .= ', '. $depthColumn;
            }
            $query .= ') VALUES (?, ?';
            if ($depthColumn) {
                $query .= ', 0';
            }
            $query .= ')';

            // Set the query parameters
            $queryParams = array($nodeId, $nodeId);

        }

        // Execute the query and close the cursor
        $entityManager
            ->getConnection()
            ->executeQuery($query, $queryParams)
            ->closeCursor();
    }
}
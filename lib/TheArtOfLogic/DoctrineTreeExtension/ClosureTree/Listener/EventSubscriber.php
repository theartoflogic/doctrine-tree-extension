<?php

namespace TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Listener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use TheArtOfLogic\DoctrineTreeExtension\Listener\EventSubscriber as BaseEventSubscriber;

/**
 * @author Sarah Ryan <sryan@phunware.com>
 */
class EventSubscriber extends BaseEventSubscriber
{
    /**
     * {@inheritdoc}
     */
    protected function getNodeClass()
    {
        return 'TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation\Node';
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata',
            'onFlush'
        );
    }

    /**
     * Track whether the class is a node.
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        // Call the parent method and check if we got data
        if ($data = parent::loadClassMetadata($eventArgs)) {

            // Split up the data into variables
            list($className, $metadata, $treeNodeAnnotation) = $data;

            // Check if we should get the tree details from another entity
            if ($treeAnnotation->entity) {

            } else {

                // Check if the treeTable was specified
                if (!$treeNodeAnnotation->table) {
                    $treeNodeAnnotation->table = $metadata['table']['name'] .'_tree';
                }

                // Check if the ancestorColumn was specified
                if (!$treeNodeAnnotation->ancestorColumn) {
                    $treeNodeAnnotation->ancestorColumn = 'ancestor';
                }

                // Check if the ancestorColumn was specified
                if (!$treeNodeAnnotation->descendantColumn) {
                    $treeNodeAnnotation->descendantColumn = 'descendant';
                }

                // Check if the withDepth parameter was specified
                if (is_null($treeNodeAnnotation->withDepth)) {
                    $treeNodeAnnotation->withDepth = true;
                }

            }

            // Set the node data
            $this->nodeData[$className] = array(
                'metadata' => $metadata,
                'annotation' => $treeNodeAnnotation
            );

        }
    }

    /**
     * {@inheritdoc}
     */
    protected function processScheduledEntityInsertion(EntityManager $entityManager, UnitOfWork $unitOfWork, $entity)
    {
        // Get the metadata
        $data = $this->getNodeData($entity);

        // Get the table name
        $treeTableName = $data['annotation']->treeTable;

        // Get the ID field anme
        $id

        // Check if the entity has a parent
        if ($parent = $entity->getParent()) {

            // Get the parent ID
            $parentId = (int)$parent->getId();

            // Format the query to insert the tree hierarchy
            $sql = '
                INSERT INTO
                    '. $treeTableName .'
                    (ancestor, descendant, depth)
                SELECT
                    ancestor, '. $id .', (depth + 1)
                FROM
                    '. $treeTableName .'
                WHERE
                    descendant = ?
                UNION ALL SELECT '. $id .', '. $id .', 0
            ';

            $query = $entityManager->createNativeQuery($sql)
                ->setParameter(1, $parentId);

        } else {

            // Format the query to insert the tree hierarchy
            $sql = 'INSERT INTO '. $table .' SET ancestor = ?, descendant = ?';

            // Get the query
            $query = $entityManager->createNativeQuery($sql)
                ->setParameter(1, $id)
                ->setParameter(2, $id);
        }

        // Execute the query
        $query->getResult();
    }

    /**
     * {@inheritdoc}
     */
    protected function processScheduledEntityUpdate(EntityManager $entityManager, UnitOfWork $unitOfWork, $entity)
    {

    }

    /**
     * {@inheritdoc}
     */
    protected function processScheduledEntityDeletion(EntityManager $entityManager, UnitOfWork $unitOfWork, $entity)
    {

    }
}
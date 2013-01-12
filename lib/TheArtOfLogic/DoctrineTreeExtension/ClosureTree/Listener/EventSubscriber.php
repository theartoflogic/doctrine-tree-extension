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
     * {@inheritdoc}
     */
    protected function processScheduledEntityInsertion(EntityManager $entityManager, UnitOfWork $unitOfWork, $entity)
    {
        // Get the metadata
        $data = $this->getNodeData($entity);

        // Get the table name
        $treeTableName = $data['annotation']->treeTable;

        // Get the ID field anme
        $idFieldName

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
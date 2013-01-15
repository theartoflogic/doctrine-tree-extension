<?php

namespace TheArtOfLogic\DoctrineTreeExtension\Listener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber as BaseEventSubscriber;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;

/**
 * @author Sarah Ryan <sryan@phunware.com>
 */
abstract class EventSubscriber implements BaseEventSubscriber
{
    /**
     * An instance of the {@link Reader} class used to read the annotations.
     *
     * @var Reader
     */
    protected $reader;

    /**
     * Process the scheduled entity insertion for the specified entity.
     *
     * @param EntityManager $entityManager
     * @param UnitOfWork $unitOfWork
     * @param object $entity The entity to process.
     */
    //abstract protected function processScheduledEntityInsertion(EntityManager $entityManager, UnitOfWork $unitOfWork, $entity);

    /**
     * Process the scheduled entity update for the specified entity.
     *
     * @param EntityManager $entityManager
     * @param UnitOfWork $unitOfWork
     * @param object $entity The entity to process.
     */
    //abstract protected function processScheduledEntityUpdate(EntityManager $entityManager, UnitOfWork $unitOfWork, $entity);

    /**
     * Process the scheduled entity deletion for the specified entity.
     *
     * @param EntityManager $entityManager
     * @param UnitOfWork $unitOfWork
     * @param object $entity The entity to process.
     */
    //abstract protected function processScheduledEntityDeletion(EntityManager $entityManager, UnitOfWork $unitOfWork, $entity);

    /**
     * Set the annotation reader.
     *
     * @param Reader $reader The annotation reader.
     */
    public function setAnnotationReader(Reader $reader)
    {
        $this->reader = $reader;

        return $this;
    }

    /**
     * Check whether the entity is a node.
     *
     * @param object The entity object.
     *
     * @return boolean Returns true if the entity is a node, otherwise returns false.
     */
    public function isNode($entity)
    {
        return (is_object($entity) && isset($entity->closureTree));
    }

    /**
     * Check whether the entity is a node tree.
     *
     * @param object The entity object.
     *
     * @return boolean Returns true if the entity is a node tree, otherwise returns false.
     */
    public function isNodeTree($entity)
    {
        return (is_object($entity) && isset($entity->closureTree));
    }

    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $entityManager = $eventArgs->getEntityManager();
        $uow = $entityManager->getUnitOfWork();

        // Loop through the scheduled insertions and check for tree nodes
        foreach ($uow->getScheduledEntityInsertions() AS $entity) {
            if ($this->isNode($entity)) {
                //$this->processScheduledEntityInsertion($entityManager, $uow, $entity);
            }
        }

        // Loop through the scheduled updates and check for tree nodes
        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
            if ($this->isNode($entity)) {
                //$this->processScheduledEntityUpdate($entityManager, $uow, $entity);
            }
        }

        // Loop through the scheduled deletions and check for tree nodes
        foreach ($uow->getScheduledEntityDeletions() AS $entity) {
            if ($this->isNode($entity)) {
                //$this->processScheduledEntityDeletion($entityManager, $uow, $entity);
            }
        }
    }
}
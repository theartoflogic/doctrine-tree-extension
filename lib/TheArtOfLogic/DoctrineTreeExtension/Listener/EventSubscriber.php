<?php

namespace TheArtOfLogic\DoctrineTreeExtension\Listener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber as BaseEventSubscriber;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

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
     * Holds the data for the node classes.
     *
     * @var array
     */
    protected $nodeData = array();

    /**
     * Get the name of the node class.
     *
     * @return string
     */
    abstract protected function getNodeClass();

    /**
     * Process the scheduled entity insertion for the specified entity.
     *
     * @param EntityManager $entityManager
     * @param UnitOfWork $unitOfWork
     * @param object $entity The entity to process.
     */
    abstract protected function processScheduledEntityInsertion(EntityManager $entityManager, UnitOfWork $unitOfWork, $entity);

    /**
     * Process the scheduled entity update for the specified entity.
     *
     * @param EntityManager $entityManager
     * @param UnitOfWork $unitOfWork
     * @param object $entity The entity to process.
     */
    abstract protected function processScheduledEntityUpdate(EntityManager $entityManager, UnitOfWork $unitOfWork, $entity);

    /**
     * Process the scheduled entity deletion for the specified entity.
     *
     * @param EntityManager $entityManager
     * @param UnitOfWork $unitOfWork
     * @param object $entity The entity to process.
     */
    abstract protected function processScheduledEntityDeletion(EntityManager $entityManager, UnitOfWork $unitOfWork, $entity);

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
        return array_key_exists(get_class($entity), $this->nodeData);
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

        // Get the class name
        $className = $metadata->getName();

        // Get the class annotations
        $annotations = $this->reader->getClassAnnotations(new \ReflectionClass($className));

        // Get the node class name
        $nodeClass = $this->getNodeClass();

        // Check if the entity uses the closure tree annotation
        foreach ($annotations as $annotation) {
            if ($annotation instanceof $nodeClass) {
                
                // Store the annotations for the node entity
                $this->nodeData[$className] = array(
                    'metadata' => $metadata,
                    'annotation' => $annotation
                );

                break;
            }
        }
    }

    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        // Loop through the scheduled insertions and check for tree nodes
        foreach ($uow->getScheduledEntityInsertions() AS $entity) {
            if ($this->isNode($entity)) {
                $this->processScheduledEntityInsertion($em, $uow, $entity);
            }
        }

        // Loop through the scheduled updates and check for tree nodes
        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
            if ($this->isNode($entity)) {
                $this->processScheduledEntityUpdate($em, $uow, $entity);
            }
        }

        // Loop through the scheduled deletions and check for tree nodes
        foreach ($uow->getScheduledEntityDeletions() AS $entity) {
            if ($this->isNode($entity)) {
                $this->processScheduledEntityDeletion($em, $uow, $entity);
            }
        }
    }

    /**
     * Get the data for the specified entity.
     *
     * @param object $entity The entity object.
     *
     * @return array Returns the array of data.
     */
    protected function getNodeData($entity)
    {
        return $this->nodeData[get_class($entity)];
    }
}
<?php

namespace TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Listener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber as BaseEventSubscriber;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

/**
 * @author Sarah Ryan <sryan@phunware.com>
 */
class EventSubscriber implements BaseEventSubscriber
{
    const NODE_CLASS = 'TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation\Node';
    const NODE_PARENT_CLASS = 'TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Annotation\NodeParent';

    /**
     * An instance of the {@link Reader} class used to read the annotations.
     *
     * @var Reader
     */
    protected $reader;

    /**
     * Holds the list of classes that are specified as closure tree nodes.
     *
     * @var array
     */
    protected $nodeClasses = array();

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
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata',
            'postLoad',
            'prePersist',
            'postPersist',
            'preUpdate',
            'postUpdate',
            'preRemove',
            'postRemove',
            'onFlush',
            'preFlush',
            'postFlush'
        );
    }

    /**
     * Check whether the specified class is a node.
     *
     * @param string $className
     *
     * @return boolean Returns true if the class is a node, otherwise returns false.
     */
    public function isNode($className)
    {
        return in_array($className, $this->nodeClasses);
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

        // Check if the entity uses the closure tree annotation
        foreach ($annotations as $annotation) {
            if ($annotation instanceof self::NODE_CLASS) {
                
                // Store the class in the list of closure tree nodes
                $this->nodeClasses[] = $className;

            }
        }
    }

    public function postLoad(EventArgs $eventArgs)
    {
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
    }

    public function postPersist(EventArgs $eventArgs)
    {
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
    }

    public function postUpdate(EventArgs $eventArgs)
    {
    }

    public function preRemove(EventArgs $eventArgs)
    {
    }

    public function postRemove(EventArgs $eventArgs)
    {
    }

    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        // Get the class metadata
        $metadata = $eventArgs->getClassMetadata();

        // Get the class name
        $className = $metadata->getName();

        // Check if the class is a node
        if ($this->isNode($className)) {

            $em = $eventArgs->getEntityManager();
            $uow = $em->getUnitOfWork();

        }
    }

    public function preFlush(PreFlushEventArgs $eventArgs)
    {
    }

    public function postFlush(PostFlushEventArgs $eventArgs)
    {
    }
}
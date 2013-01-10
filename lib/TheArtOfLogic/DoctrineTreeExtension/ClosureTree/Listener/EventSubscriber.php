<?php

namespace TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Listener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventSubscriber as BaseEventSubscriber;
use Doctrine\Common\EventArgs;

/**
 * @author Sarah Ryan <sryan@phunware.com>
 */
class EventSubscriber implements BaseEventSubscriber
{
    /**
     * An instance of the {@link Reader} class used to read the annotations.
     *
     * @var Reader
     */
    protected $reader;

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

    public function loadClassMetadata(EventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
    }

    public function postLoad(EventArgs $eventArgs)
    {
    }

    public function prePersist(EventArgs $eventArgs)
    {
    }

    public function postPersist(EventArgs $eventArgs)
    {
    }

    public function preRemove(EventArgs $eventArgs)
    {
    }

    public function postRemove(EventArgs $eventArgs)
    {
    }

    public function onFlush(EventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
    }
}
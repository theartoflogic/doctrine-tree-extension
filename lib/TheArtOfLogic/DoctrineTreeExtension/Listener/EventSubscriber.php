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
     * Set the annotation reader.
     *
     * @param Reader $reader The annotation reader.
     */
    public function setAnnotationReader(Reader $reader)
    {
        $this->reader = $reader;

        return $this;
    }
}
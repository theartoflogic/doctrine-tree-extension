<?php

namespace TheArtOfLogic\DoctrineTreeExtensionTest\Listener;

use TheArtOfLogic\DoctrineTreeExtensionTest\Base;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;

abstract class EventSubscriberTest extends Base
{
    /**
     * Test that the events are subscribed to properly.
     */
    public function testAddEventSubscriber()
    {
        // Loop through the list of subscribed events and make sure it exists
        foreach ($this->getSubscribedEvents() as $event) {
            $this->assertTrue($this->getEventManager()->hasListeners($event));
        }
    }
}
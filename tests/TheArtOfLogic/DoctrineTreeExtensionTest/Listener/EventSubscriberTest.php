<?php

namespace TheArtOfLogic\DoctrineTreeExtensionTest\Listener;

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;

abstract class EventSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Configuration
     */
    protected $entityManagerConfig;

    /**
     * @var EventSubscriber
     */
    protected $eventSubscriber;

    /**
     * @var AnnotationDriver
     */
    protected $annotationDriver;

    public function setup()
    {
        // Add the event subscriber
        $this->getEventManager()->addEventSubscriber($this->getEventSubscriber());
    }

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
    
    /**
     * Get the list of subscribed events.
     *
     * @return array Returns an array containing the list of subscribed events.
     */
    abstract protected function getSubscribedEvents();
    
    /**
     * Get the list of entity classes to use.
     *
     * @return array Returns an array containing the list of entity classes to use.
     */
    abstract protected function getEntityClasses();

    /**
     * Get the event manager.
     *
     * @return EventManager Returns an instance of the EventManager class.
     */
    protected function getEventManager()
    {
        // Check if the event manager has already been initialized
        if (!$this->eventManager) {
            $this->eventManager = new EventManager();
        }

        return $this->eventManager;
    }

    /**
     * Get the event subscriber.
     *
     * @return EventSubscriber Returns an instance of the EventSubscriber class.
     */
    protected function getEventSubscriber()
    {
        // Check if the event subscriber has already been initialized
        if (!$this->eventSubscriber) {

            // Get the reflection class
            $reflectionClass = new \ReflectionClass($this->getEventSubscriberClass());

            // Get the class instance
            $this->eventSubscriber = $reflectionClass->newInstance();

            // Set the annotation reader
            $this->eventSubscriber->setAnnotationReader($this->getAnnotationReader());
        }

        return $this->eventSubscriber;
    }

    /**
     * Returns a mock sqlite entity manager.
     *
     * @param EventManager $evm
     * 
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        if (!$this->entityManager) {

            $params = array(
                'driver' => 'pdo_sqlite',
                'memory' => true
            );

            $entityManager = EntityManager::create($params, $this->getEntityManagerConfiguration(), $this->getEventManager());

            // Get the metadata for each entity used
            $schema = array_map(function($class) use ($entityManager) {
                return $entityManager->getClassMetadata($class);
            }, $this->getEntityClasses());

            // Make sure the schema exists
            $schemaTool = new SchemaTool($entityManager);
            $schemaTool->dropSchema(array());
            $schemaTool->createSchema($schema);
           
            $this->entityManager = $entityManager;
        }

        return $this->entityManager;
    }

    /**
     * Get the entity manager configuration.
     *
     * @return Configuration Returns the configurations to use for the entity manager.
     */
    protected function getEntityManagerConfiguration()
    {
        // Check if the configurations have been initialized
        if (!$this->entityManagerConfig) {

            // The name of the configuration class to use
            $configurationClass = 'Doctrine\ORM\Configuration';

            // Get the list of methods the configuration class has
            $reflectionClass = new \ReflectionClass($configurationClass);
            $classMethods = $reflectionClass->getMethods();

            // Holds the list of methods to mock
            $mockMethods = array();

            // Get the list of methods to mock
            foreach ($classMethods as $classMethod) {
                if ($classMethod->name !== 'addFilter' && $classMethod->name !== 'getFilterClassName') {
                    $mockMethods[] = $classMethod->name;
                }
            }

            // Initialize the configuration object
            $this->entityManagerConfig = $this->getMock($configurationClass, $mockMethods);

            $this->entityManagerConfig->expects($this->once())
                ->method('getProxyDir')
                ->will($this->returnValue(__DIR__ .'/../../tmp'));

            $this->entityManagerConfig->expects($this->once())
                ->method('getProxyNamespace')
                ->will($this->returnValue('Proxy'));

            $this->entityManagerConfig->expects($this->once())
                ->method('getAutoGenerateProxyClasses')
                ->will($this->returnValue(true));

            $this->entityManagerConfig->expects($this->once())
                ->method('getClassMetadataFactoryName')
                ->will($this->returnValue('Doctrine\ORM\Mapping\ClassMetadataFactory'));

            $this->entityManagerConfig->expects($this->any())
                ->method('getMetadataDriverImpl')
                ->will($this->returnValue($this->getMetadataDriverImplementation()));

            $this->entityManagerConfig->expects($this->any())
                ->method('getDefaultRepositoryClassName')
                ->will($this->returnValue('Doctrine\ORM\EntityRepository'));

            $this->entityManagerConfig->expects($this->any())
                ->method('getQuoteStrategy')
                ->will($this->returnValue(new DefaultQuoteStrategy()));

            $this->entityManagerConfig->expects($this->any())
                ->method('getNamingStrategy')
                ->will($this->returnValue(new DefaultNamingStrategy()));

        }

        return $this->entityManagerConfig;
    }

    /**
     * Creates default mapping driver
     *
     * @return AnnotationDriver
     */
    protected function getMetadataDriverImplementation()
    {
        if (!$this->annotationDriver) {
            $this->annotationDriver = new AnnotationDriver($this->getAnnotationReader());
        }

        return $this->annotationDriver;
    }

    /**
     * Get the annotation reader.
     *
     * @return AnnotationReader
     */
    protected function getAnnotationReader()
    {
        return $GLOBALS['annotationReader'];
    }
}
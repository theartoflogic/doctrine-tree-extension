<?php

namespace TheArtOfLogic\DoctrineTreeExtensionTest;

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;

abstract class Base extends \PHPUnit_Framework_TestCase
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

    /**
     * @var array
     */
    protected $usedTables;

    /**
     * @var SchemaTool
     */
    protected $schemaTool;

    public function setUp()
    {
        // Make sure the sqlite extension is loaded
        if (!extension_loaded('pdo_sqlite')) {
            throw new \Exception('You need the sqlite extension to run this test.');
        }

        // Add the event subscriber
        $this->getEventManager()->addEventSubscriber($this->getEventSubscriber());
    }

    public function tearDown()
    {
        if ($this->usedTables) {

            // Drop the used tables
            $this->dropTables($this->usedTables);

            $this->usedTables = null;
        }
    }
    
    /**
     * Get the list of subscribed events.
     *
     * @return array Returns an array containing the list of subscribed events.
     */
    abstract protected function getSubscribedEvents();

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

            $params = array(
                'driver' => 'pdo_mysql',
                'host' => 'mysqldb',
                'user' => 'phunware',
                'password' => 'phunware10',
                'dbname' => 'test'
            );

            $entityManager = EntityManager::create($params, $this->getEntityManagerConfiguration(), $this->getEventManager());
           
            $this->entityManager = $entityManager;
        }

        return $this->entityManager;
    }

    protected function useTables(array $classes)
    {
        // Set the list of used tables
        $this->usedTables = $classes;

        // Create the tables
        $this->createTables($classes);
    }

    /**
     * Create the database tables for the specified entity classes.
     *
     * @param array $classes An array containing the list of class names for each entity.
     */
    protected function createTables(array $classes)
    {
        $entityManager = $this->getEntityManager();

        // Get the metadata for each entity used
        $schema = array_map(function($class) use ($entityManager) {
            return $entityManager->getClassMetadata($class);
        }, $classes);

        // Setup the database
        $schemaTool = $this->getSchemaTool();
        $schemaTool->dropSchema($schema);
        $schemaTool->createSchema($schema);
    }

    /**
     * Drop the database tables for the specified entity classes.
     *
     * @param array $classes An array containing the list of class names for each entity.
     */
    protected function dropTables(array $classes)
    {
        $entityManager = $this->getEntityManager();
        
        // Get the metadata for each entity used
        $schema = array_map(function($class) use ($entityManager) {
            return $entityManager->getClassMetadata($class);
        }, $classes);

        // Setup the database
        $schemaTool = $this->getSchemaTool();
        $schemaTool->dropSchema($schema);
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
            $config = $this->getMock($configurationClass, $mockMethods);

            $config->expects($this->once())
                ->method('getProxyDir')
                ->will($this->returnValue(__DIR__ .'/../../../tmp'));

            $config->expects($this->once())
                ->method('getProxyNamespace')
                ->will($this->returnValue('Proxy'));

            $config->expects($this->once())
                ->method('getAutoGenerateProxyClasses')
                ->will($this->returnValue(true));

            $config->expects($this->once())
                ->method('getClassMetadataFactoryName')
                ->will($this->returnValue('Doctrine\ORM\Mapping\ClassMetadataFactory'));

            $config->expects($this->any())
                ->method('getMetadataDriverImpl')
                ->will($this->returnValue($this->getMetadataDriverImplementation()));

            $config->expects($this->any())
                ->method('getDefaultRepositoryClassName')
                ->will($this->returnValue('Doctrine\ORM\EntityRepository'));

            $config->expects($this->any())
                ->method('getQuoteStrategy')
                ->will($this->returnValue(new DefaultQuoteStrategy()));

            $config->expects($this->any())
                ->method('getNamingStrategy')
                ->will($this->returnValue(new DefaultNamingStrategy()));

            // Set the config object
            $this->entityManagerConfig = $config;
        }

        return $this->entityManagerConfig;
    }

    /**
     * Get the schema tool.
     *
     * @return SchemaTool
     */
    protected function getSchemaTool()
    {
        if (!$this->schemaTool) {
            $this->schemaTool = new SchemaTool($this->getEntityManager());
        }

        return $this->schemaTool;
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
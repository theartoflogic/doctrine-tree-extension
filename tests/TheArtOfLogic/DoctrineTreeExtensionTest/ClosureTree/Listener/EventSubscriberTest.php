<?php

namespace TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Listener;

use TheArtOfLogic\DoctrineTreeExtensionTest\Listener\EventSubscriberTest as BaseEventSubscriberTest;
use TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\Category;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;

class EventSubscriberTest extends BaseEventSubscriberTest
{
    const CATEGORY_CLASS = 'TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\Category';

    /**
     * This tests that the tree relationships are added
     * properly when persisting a new root node.
     */
    public function testPersistRootNode()
    {
        // Get the entity manager
        $entityManager = $this->getEntityManager();

        // Get the entity repository
        $repository = $entityManager->getRepository(self::CATEGORY_CLASS);

        // Initialize the entity
        $category = new Category();
        $category->setName('Acme');

        // Persist the entity
        $entityManager->persist($category);
        $entityManager->flush();
    }

    /**
     * This tests that the tree relationships are added
     * properly when persisting a new child node.
     */
    public function testPersistChildNode()
    {
        // Get the entity manager
        $entityManager = $this->getEntityManager();

        // Get the entity repository
        $repository = $entityManager->getRepository(self::CATEGORY_CLASS);

        echo ('Count: '. count($repository->findAll()));

        // Get the parent node
        $parent = $repository->findOneById(1);

        /*
        $count = $repository->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();

        die('Count: '. $count);
        */

        // Initialize the entity
        $category = new Category();
        $category->setName('Acme Child');
        $category->setParent($parent);

        // Persist the entity
        $entityManager->persist($category);
        $entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    protected function getEventSubscriberClass()
    {
        return 'TheArtOfLogic\DoctrineTreeExtension\ClosureTree\Listener\EventSubscriber';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityClasses()
    {
        return array(
            'TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\Category'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata',
            'onFlush'
        );
    }
}
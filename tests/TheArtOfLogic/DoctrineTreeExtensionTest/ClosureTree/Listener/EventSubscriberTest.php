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
    // Paths to entity classes
    const CATEGORY = 'TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\Category';
    const CATEGORY_WITH_TREE = 'TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\CategoryWithTree';
    const CATEGORY_WITH_DEPTH_TREE = 'TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\CategoryWithDepthTree';
    const CATEGORY_TREE = 'TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\CategoryTree';
    const CATEGORY_DEPTH_TREE = 'TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\CategoryDepthTree';

    /**
     * This tests that the tree relationships are added
     * properly when persisting a new root node.
     */
    public function testPersistRootNode()
    {
        // Get the entity manager
        $entityManager = $this->getEntityManager();

        // Initialize the entity
        $category = new Category();
        $category->setName('iOS');

        // Persist the entity
        $entityManager->persist($category);
        $entityManager->flush();

        // Get the entity repository
        $repository = $entityManager->getRepository(self::CATEGORY);

        // Find root nodes
        $numRootNodes = $repository->findRootNodeCount();

        // Make sure there is 1 root node
        $this->assertEquals(1, $numRootNodes);
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
        $repository = $entityManager->getRepository(self::CATEGORY);

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
    /*
    protected function populateData($withDepth)
    {
        // iOS
        $ios = new Category();
        $ios->setName('iOS');

        $iphone = new Category();
        $iphone->setName('iPhone');
        $iphone->setParent($ios);

        $iphone4 = new Category();
        $iphone4->setName('iPhone 4');
        $iphone4->setParent($iphone);

        $iphone4S = new Category();
        $iphone4S->setName('iPhone 4S');
        $iphone4S->setParent($iphone);

        $iphone5 = new Category();
        $iphone5->setName('iPhone 5');
        $iphone5->setParent($iphone);

        $ipad = new Category();
        $ipad->setName('iPad');
        $ipad->setParent($ios);

        $ipadMini = new Category();
        $ipadMini->setName('iPad Mini');
        $ipadMini->setParent($ipad);

        // Android
        $android = new Category();
        $android->setName('Android');

        $androidPhone = new Category();
        $androidPhone->setName('Phone');
        $androidPhone->setParent($android);

        $htcOneX = new Category();
        $htcOneX->setName('HTC One X');
        $htcOneX->setParent($androidPhone);

        $galaxy3S = new Category();
        $galaxy3S->setName('Samsung Galaxy III S');
        $galaxy3S->setParent($androidPhone);

        $androidTablet = new Category();
        $androidTablet->setName('Tablet');
        $androidTablet->setParent($android);

        $nexus7 = new Category();
        $nexus7->setName('Google Nexus 7');
        $nexus7->setParent($androidTablet);

        $galaxyNote = new Category();
        $galaxyNote->setName('Samsung Galaxy Note');
        $galaxyNote->setParent($androidTablet);

        // Windows
        $windows = new Category();
        $windows->setName('Windows');

        $windowsPhone = new Category();
        $windowsPhone->setName('Phone');
        $windowsPhone->setParent($windows);

        $nokiaLumia900 = new Category();
        $nokiaLumia900->setName('Nokia Lumia 900');
        $nokiaLumia900->setParent($windowsPhone);

        $windowsTablet = new Category();
        $windowsTablet->setName('Tablet');
        $windowsTablet->setParent($windows);

        $surface = new Category();
        $surface->setName('Windows Surface');
        $surface->setParent($windowsTablet);

        $series7 = new Category();
        $series7->setName('Samsumg Series 7');
        $series7->setParent($windowsTablet);

        // Get the entity manager
        $entityManager = $this->getEntityManager();

        // Persist the data
        $entityManager->persist($ios);
        $entityManager->persist($iphone);
        $entityManager->persist($iphone4);
        $entityManager->persist($iphone4S);
        $entityManager->persist($iphone5);
        $entityManager->persist($ipad);
        $entityManager->persist($ipadMini);

        $entityManager->persist($android);
        $entityManager->persist($androidPhone);
        $entityManager->persist($htcOneX);
        $entityManager->persist($galaxy3S);
        $entityManager->persist($androidTablet);
        $entityManager->persist($nexus7);
        $entityManager->persist($galaxyNote);

        $entityManager->persist($windows);
        $entityManager->persist($windowsPhone);
        $entityManager->persist($nokiaLumia900);
        $entityManager->persist($windowsTablet);
        $entityManager->persist($surface);
        $entityManager->persist($series7);

        // Save the data
        $entityManager->flush();
    }
    */

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
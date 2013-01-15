<?php

namespace TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Listener;

use TheArtOfLogic\DoctrineTreeExtensionTest\Listener\EventSubscriberTest as BaseEventSubscriberTest;
use TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\Category;
use TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\CategoryWithTree;
use TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\CategoryWithDepthTree;
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

    public function testPersistRootNode_WithoutDepth()
    {
        // Create the database tables
        $this->createTables(array(
            self::CATEGORY,
            self::CATEGORY_TREE
        ));

        // Get the entity manager
        $entityManager = $this->getEntityManager();

        // Initialize the entities
        $ios = new Category();
        $ios->setName('iOS');

        // Persist the entity
        $entityManager->persist($ios);
        $entityManager->flush();

        // Get the entity repository
        $nodeRepository = $entityManager->getRepository(self::CATEGORY);
        $treeRepository = $entityManager->getRepository(self::CATEGORY_TREE);

        // Make sure there is the correct number of root nodes
        $this->assertEquals(1, $nodeRepository->findRootNodeCount());

        // Make sure the tree row exists
        $treeExists = $treeRepository->createQueryBuilder('t')
            ->select('COUNT(t.ancestor)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(1, $treeExists);

        // Drop the database tables
        $this->dropTables(array(
            self::CATEGORY,
            self::CATEGORY_TREE
        ));
    }

    public function testPersistRootNode_WithoutDepth_WithTree()
    {
        // Create the database tables
        $this->createTables(array(
            self::CATEGORY_WITH_TREE,
            self::CATEGORY_TREE
        ));

        // Get the entity manager
        $entityManager = $this->getEntityManager();

        // Initialize the entities
        $ios = new CategoryWithTree();
        $ios->setName('iOS');

        // Persist the entity
        $entityManager->persist($ios);
        $entityManager->flush();

        // Get the entity repository
        $nodeRepository = $entityManager->getRepository(self::CATEGORY_WITH_TREE);
        $treeRepository = $entityManager->getRepository(self::CATEGORY_TREE);

        // Make sure there is the correct number of root nodes
        $this->assertEquals(1, $nodeRepository->findRootNodeCount());

        // Make sure the tree row exists
        $treeExists = $treeRepository->createQueryBuilder('t')
            ->select('COUNT(t.ancestor)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(1, $treeExists);

        // Drop the database tables
        $this->dropTables(array(
            self::CATEGORY_WITH_TREE,
            self::CATEGORY_TREE
        ));
    }

    public function testPersistRootNode_WithDepth()
    {
        // Create the database tables
        $this->createTables(array(
            self::CATEGORY_WITH_DEPTH_TREE,
            self::CATEGORY_DEPTH_TREE
        ));

        // Get the entity manager
        $entityManager = $this->getEntityManager();

        // Initialize the entities
        $ios = new CategoryWithDepthTree();
        $ios->setName('iOS');

        // Persist the entity
        $entityManager->persist($ios);
        $entityManager->flush();

        // Get the entity repository
        $nodeRepository = $entityManager->getRepository(self::CATEGORY_WITH_DEPTH_TREE);
        $treeRepository = $entityManager->getRepository(self::CATEGORY_DEPTH_TREE);

        // Make sure there is the correct number of root nodes
        $this->assertEquals(1, $nodeRepository->findRootNodeCount());

        // Make sure the tree row exists
        $treeExists = $treeRepository->createQueryBuilder('t')
            ->select('COUNT(t.ancestor)')
            ->where('t.depth = 0')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(1, $treeExists);

        // Drop the database tables
        $this->dropTables(array(
            self::CATEGORY,
            self::CATEGORY_TREE
        ));
    }

    public function testPersistChildNode_WithoutDepth()
    {
        // Create the database tables
        $this->createTables(array(
            self::CATEGORY,
            self::CATEGORY_TREE
        ));

        // Get the entity manager
        $entityManager = $this->getEntityManager();

        // Get the entity repository
        $nodeRepository = $entityManager->getRepository(self::CATEGORY);
        $treeRepository = $entityManager->getRepository(self::CATEGORY_TREE);

        // Initialize the entities
        $ios = new Category();
        $ios->setName('iOS');

        $iphone = new Category();
        $iphone->setName('iPhone');
        $iphone->setParent($ios);

        $ipad = new Category();
        $ipad->setName('iPad');
        $ipad->setParent($ios);

        // Persist the entities
        $entityManager->persist($ios);
        $entityManager->persist($iphone);
        $entityManager->persist($ipad);
        $entityManager->flush();

        // Test assertions
        $this->assertEquals(1, $nodeRepository->findRootNodeCount());
        $this->assertEquals(2, $nodeRepository->findChildNodeCount($ios->getId()));

        // Make sure the tree rows exists
        $treeExists = $treeRepository->createQueryBuilder('t')
            ->select('COUNT(t.ancestor)')
            ->where('t.ancestor != t.descendant')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(2, $treeExists);

        // Drop the database tables
        $this->dropTables(array(
            self::CATEGORY,
            self::CATEGORY_TREE
        ));
    }

    public function testPersistChildNode_WithDepth()
    {
        // Create the database tables
        $this->createTables(array(
            self::CATEGORY_WITH_DEPTH_TREE,
            self::CATEGORY_DEPTH_TREE
        ));

        // Get the entity manager
        $entityManager = $this->getEntityManager();

        // Get the entity repository
        $nodeRepository = $entityManager->getRepository(self::CATEGORY_WITH_DEPTH_TREE);
        $treeRepository = $entityManager->getRepository(self::CATEGORY_DEPTH_TREE);

        // Initialize the entities
        $ios = new CategoryWithDepthTree();
        $ios->setName('iOS');

        $iphone = new CategoryWithDepthTree();
        $iphone->setName('iPhone');
        $iphone->setParent($ios);

        $ipad = new CategoryWithDepthTree();
        $ipad->setName('iPad');
        $ipad->setParent($ios);

        // Persist the entities
        $entityManager->persist($ios);
        $entityManager->persist($iphone);
        $entityManager->persist($ipad);
        $entityManager->flush();

        // Test assertions
        $this->assertEquals(1, $nodeRepository->findRootNodeCount());
        $this->assertEquals(2, $nodeRepository->findChildNodeCount($ios->getId()));

        // Make sure the tree rows exists
        $treeExists = $treeRepository->createQueryBuilder('t')
            ->select('COUNT(t.ancestor)')
            ->where('t.depth > 0')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(2, $treeExists);

        // Drop the database tables
        $this->dropTables(array(
            self::CATEGORY,
            self::CATEGORY_TREE
        ));
    }

    public function testUpdateParentNode_WithoutDepth()
    {
        // Create the database tables
        $this->createTables(array(
            self::CATEGORY,
            self::CATEGORY_TREE
        ));

        // Get the entity manager
        $entityManager = $this->getEntityManager();

        // Get the entity repository
        $nodeRepository = $entityManager->getRepository(self::CATEGORY);
        $treeRepository = $entityManager->getRepository(self::CATEGORY_TREE);

        // Initialize the entities
        $ios = new Category();
        $ios->setName('iOS');

        $android = new Category();
        $android->setName('Android');

        $iphone = new Category();
        $iphone->setName('iPhone');
        $iphone->setParent($android);

        // Persist the entities
        $entityManager->persist($ios);
        $entityManager->persist($android);
        $entityManager->persist($iphone);
        $entityManager->flush();

        // Make sure iPhone is a descendant of Android
        $treeExists = $treeRepository->createQueryBuilder('t')
            ->select('COUNT(t.ancestor)')
            ->where('t.ancestor = :ancestor')
            ->andWhere('t.descendant = :descendant')
            ->setParameter('ancestor', $android->getId())
            ->setParameter('descendant', $iphone->getId())
            ->getQuery()
            ->getSingleScalarResult();

        // Make sure the current parent for iPhone is Android
        $this->assertEquals($iphone->getParent()->getId(), $android->getId());
        $this->assertEquals(1, $treeExists);

        // Update the parent
        $iphone->setParent($ios);

        $entityManager->persist($iphone);
        $entityManager->flush();

        // Make sure iPhone is a descendant of iOS
        $treeExists = $treeRepository->createQueryBuilder('t')
            ->select('COUNT(t.ancestor)')
            ->where('t.ancestor = :ancestor')
            ->andWhere('t.descendant = :descendant')
            ->setParameter('ancestor', $ios->getId())
            ->setParameter('descendant', $iphone->getId())
            ->getQuery()
            ->getSingleScalarResult();

        // Make sure the current parent for iPhone is iOS
        $this->assertEquals($iphone->getParent()->getId(), $ios->getId());
        $this->assertEquals(1, $treeExists);

        // Drop the database tables
        $this->dropTables(array(
            self::CATEGORY,
            self::CATEGORY_TREE
        ));
    }

    public function testUpdateParentNode_WithDepth()
    {
        // Create the database tables
        $this->createTables(array(
            self::CATEGORY_WITH_DEPTH_TREE,
            self::CATEGORY_DEPTH_TREE
        ));

        // Get the entity manager
        $entityManager = $this->getEntityManager();

        // Get the entity repository
        $nodeRepository = $entityManager->getRepository(self::CATEGORY_WITH_DEPTH_TREE);
        $treeRepository = $entityManager->getRepository(self::CATEGORY_DEPTH_TREE);

        // Initialize the entities
        $ios = new CategoryWithDepthTree();
        $ios->setName('iOS');

        $android = new CategoryWithDepthTree();
        $android->setName('Android');

        $iphone = new CategoryWithDepthTree();
        $iphone->setName('iPhone');
        $iphone->setParent($android);

        // Persist the entities
        $entityManager->persist($ios);
        $entityManager->persist($android);
        $entityManager->persist($iphone);
        $entityManager->flush();

        // Make sure iPhone is a descendant of Android
        $treeExists = $treeRepository->createQueryBuilder('t')
            ->select('COUNT(t.ancestor)')
            ->where('t.ancestor = :ancestor')
            ->andWhere('t.descendant = :descendant')
            ->andWhere('t.depth = 1')
            ->setParameter('ancestor', $android->getId())
            ->setParameter('descendant', $iphone->getId())
            ->getQuery()
            ->getSingleScalarResult();

        // Make sure the current parent for iPhone is Android
        $this->assertEquals($iphone->getParent()->getId(), $android->getId());
        $this->assertEquals(1, $treeExists);

        // Update the parent
        $iphone->setParent($ios);

        $entityManager->persist($iphone);
        $entityManager->flush();

        // Make sure iPhone is a descendant of iOS
        $treeExists = $treeRepository->createQueryBuilder('t')
            ->select('COUNT(t.ancestor)')
            ->where('t.ancestor = :ancestor')
            ->andWhere('t.descendant = :descendant')
            ->andWhere('t.depth = 1')
            ->setParameter('ancestor', $ios->getId())
            ->setParameter('descendant', $iphone->getId())
            ->getQuery()
            ->getSingleScalarResult();

        // Make sure the current parent for iPhone is iOS
        $this->assertEquals($iphone->getParent()->getId(), $ios->getId());
        $this->assertEquals(1, $treeExists);

        // Drop the database tables
        $this->dropTables(array(
            self::CATEGORY_WITH_DEPTH_TREE,
            self::CATEGORY_DEPTH_TREE
        ));
    }

    public function testDeleteRootNode_WithoutDepth()
    {
        // Create the database tables
        $this->createTables(array(
            self::CATEGORY,
            self::CATEGORY_TREE
        ));

        // Get the entity manager
        $entityManager = $this->getEntityManager();

        // Initialize the entities
        $ios = new Category();
        $ios->setName('iOS');

        // Initialize the entities
        $iphone = new Category();
        $iphone->setName('iPhone');
        $iphone->setParent($ios);

        // Persist the entity
        $entityManager->persist($ios);
        $entityManager->persist($iphone);
        $entityManager->flush();

        // Now delete the root node
        $entityManager->remove($ios);
        $entityManager->flush();

        // Get the entity repository
        $nodeRepository = $entityManager->getRepository(self::CATEGORY);
        $treeRepository = $entityManager->getRepository(self::CATEGORY_TREE);

        // Make sure all nodes were deleted
        $this->assertEquals(0, $nodeRepository->findNodeCount());

        // Make sure all tree rows were deleted
        $treeExists = $treeRepository->createQueryBuilder('t')
            ->select('COUNT(t.ancestor)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(0, $treeExists);

        // Drop the database tables
        $this->dropTables(array(
            self::CATEGORY,
            self::CATEGORY_TREE
        ));
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
    protected function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata',
            'onFlush'
        );
    }
}
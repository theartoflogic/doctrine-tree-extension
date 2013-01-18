<?php

namespace TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\Repository;

use TheArtOfLogic\DoctrineTreeExtensionTest\Base;
use TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\Category;
use TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\CategoryWithTree;
use TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\CategoryWithDepthTree;

class EntityRepository extends Base
{
    // Paths to entity classes
    const CATEGORY = 'TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\Category';
    const CATEGORY_WITH_TREE = 'TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\CategoryWithTree';
    const CATEGORY_WITH_DEPTH_TREE = 'TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\CategoryWithDepthTree';
    const CATEGORY_TREE = 'TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\CategoryTree';
    const CATEGORY_DEPTH_TREE = 'TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\CategoryDepthTree';

    public function testFindRootNodes()
    {
        $this->useTables(array(
            self::CATEGORY_WITH_DEPTH_TREE,
            self::CATEGORY_DEPTH_TREE
        ));

        // Get the entity manager
        $entityManager = $this->getEntityManager();

        // Initialize the entities
        $ios = new CategoryWithDepthTree();
        $ios->setName('iOS');

        $android = new CategoryWithDepthTree();
        $android->setName('Android');

        // Persist the entity
        $entityManager->persist($ios);
        $entityManager->persist($android);
        $entityManager->flush();

        // Get the entity repository
        $nodeRepository = $entityManager->getRepository(self::CATEGORY_WITH_DEPTH_TREE);

        // Make sure there is the correct number of root nodes
        $this->assertEquals(2, $nodeRepository->findRootNodeCount());
    }

    public function testFindChildNodes()
    {
        $this->useTables(array(
            self::CATEGORY_WITH_DEPTH_TREE,
            self::CATEGORY_DEPTH_TREE
        ));

        // Get the entity manager
        $entityManager = $this->getEntityManager();

        // Initialize the entities
        $ios = new CategoryWithDepthTree();
        $ios->setName('iOS');

        $iphone = new CategoryWithDepthTree();
        $iphone->setName('iPhone');
        $iphone->setParent($ios);

        $ipad = new CategoryWithDepthTree();
        $ipad->setName('iPad');
        $ipad->setParent($ios);

        $ipadMini = new CategoryWithDepthTree();
        $ipadMini->setName('iPad Mini');
        $ipadMini->setParent($ipad);

        $android = new CategoryWithDepthTree();
        $android->setName('Android');

        // Persist the entity
        $entityManager->persist($ios);
        $entityManager->persist($iphone);
        $entityManager->persist($ipad);
        $entityManager->persist($ipadMini);
        $entityManager->persist($android);
        $entityManager->flush();

        // Get the entity repository
        $nodeRepository = $entityManager->getRepository(self::CATEGORY_WITH_DEPTH_TREE);

        // Make sure there is the correct number of root nodes
        $this->assertEquals(2, $nodeRepository->findChildNodeCount($ios->getId()));
        $this->assertEquals(1, $nodeRepository->findChildNodeCount($ipad->getId()));
        $this->assertEquals(0, $nodeRepository->findChildNodeCount($ipadMini->getId()));
        $this->assertEquals(0, $nodeRepository->findChildNodeCount($android->getId()));
    }

    public function testFindChildHierarchy()
    {
        $this->useTables(array(
            self::CATEGORY_WITH_DEPTH_TREE,
            self::CATEGORY_DEPTH_TREE
        ));

        // Get the entity manager
        $entityManager = $this->getEntityManager();

        // Initialize the entities
        $ios = new CategoryWithDepthTree();
        $ios->setName('iOS');

        $iphone = new CategoryWithDepthTree();
        $iphone->setName('iPhone');
        $iphone->setParent($ios);

        $ipad = new CategoryWithDepthTree();
        $ipad->setName('iPad');
        $ipad->setParent($ios);

        $ipadMini = new CategoryWithDepthTree();
        $ipadMini->setName('iPad Mini');
        $ipadMini->setParent($ipad);

        $android = new CategoryWithDepthTree();
        $android->setName('Android');

        // Persist the entity
        $entityManager->persist($ios);
        $entityManager->persist($iphone);
        $entityManager->persist($ipad);
        $entityManager->persist($ipadMini);
        $entityManager->persist($android);
        $entityManager->flush();

        // Get the entity repository
        $nodeRepository = $entityManager->getRepository(self::CATEGORY_WITH_DEPTH_TREE);

        $childHierarchy = $nodeRepository->getChildHierarchyQueryBuilder($ios->getId(), true)
            ->select('node.id')
            ->orderBy('tree.depth')
            ->getQuery()
            ->getScalarResult();

        $ids = array_map('current', $childHierarchy);

        // Make sure there is the correct number of root nodes
        $this->assertEquals(array(
            $ios->getId(),
            $iphone->getId(),
            $ipad->getId(),
            $ipadMini->getId()
        ), $ids);
    }

    public function testFindParentHierarchy()
    {
        $this->useTables(array(
            self::CATEGORY_WITH_DEPTH_TREE,
            self::CATEGORY_DEPTH_TREE
        ));

        // Get the entity manager
        $entityManager = $this->getEntityManager();

        // Initialize the entities
        $ios = new CategoryWithDepthTree();
        $ios->setName('iOS');

        $iphone = new CategoryWithDepthTree();
        $iphone->setName('iPhone');
        $iphone->setParent($ios);

        $ipad = new CategoryWithDepthTree();
        $ipad->setName('iPad');
        $ipad->setParent($ios);

        $ipadMini = new CategoryWithDepthTree();
        $ipadMini->setName('iPad Mini');
        $ipadMini->setParent($ipad);

        $android = new CategoryWithDepthTree();
        $android->setName('Android');

        // Persist the entity
        $entityManager->persist($ios);
        $entityManager->persist($iphone);
        $entityManager->persist($ipad);
        $entityManager->persist($ipadMini);
        $entityManager->persist($android);
        $entityManager->flush();

        // Get the entity repository
        $nodeRepository = $entityManager->getRepository(self::CATEGORY_WITH_DEPTH_TREE);

        // Make sure there is the correct number of root nodes for the iPad Mini
        $parentHierarchy = $nodeRepository->getParentHierarchyQueryBuilder($ipadMini->getId(), true)
            ->select('node.id')
            ->orderBy('tree.depth', 'DESC')
            ->getQuery()
            ->getScalarResult();

        $ids = array_map('current', $parentHierarchy);

        $this->assertEquals(array(
            $ios->getId(),
            $ipad->getId(),
            $ipadMini->getId()
        ), $ids);

        // Make sure there is the correct number of root nodes for the iPhone
        $parentHierarchy = $nodeRepository->getParentHierarchyQueryBuilder($iphone->getId(), true)
            ->select('node.id')
            ->orderBy('tree.depth', 'DESC')
            ->getQuery()
            ->getScalarResult();

        $ids = array_map('current', $parentHierarchy);

        // Make sure there is the correct number of root nodes
        $this->assertEquals(array(
            $ios->getId(),
            $iphone->getId()
        ), $ids);
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
    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata',
            'postPersist',
            'preUpdate',
            'preRemove'
        );
    }
}
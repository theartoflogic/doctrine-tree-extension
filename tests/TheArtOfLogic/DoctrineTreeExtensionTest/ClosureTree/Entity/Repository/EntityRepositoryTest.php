<?php

namespace TheArtOfLogic\DoctrineTreeExtensionTest\ClosureTree\Entity\Repository;

use TheArtOfLogic\DoctrineTreeExtensionTest\Base;

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

        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository(self::CATEGORY);

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
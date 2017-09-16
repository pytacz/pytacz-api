<?php

namespace ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use ApiBundle\Entity\Notebook;

class LoadNotebookData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load notebook into test database
     *
     * @param ObjectManager $manager
     *
     * @return Notebook
     */
    public function load(ObjectManager $manager)
    {
        $notebook = new Notebook();
        $notebook->setName('TestNotebook');
        $notebook->setPrivate(true);
        $notebook->setUser($this->getReference('user'));

        $manager->persist($notebook);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            LoadUserData::class,
        ];
    }
}

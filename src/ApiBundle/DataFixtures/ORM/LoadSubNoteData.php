<?php

namespace ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use ApiBundle\Entity\SubNote;

class LoadSubNoteData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
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
     * Load subNote into test database
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $note = new SubNote();
        $note->setName('TestNote');
        $note->setContent('test content');
        $note->setAskable(true);
        $note->setNote($this->getReference('note'));

        $manager->persist($note);
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            LoadUserData::class,
            LoadNotebookData::class,
            LoadNoteData::class
        ];
    }
}

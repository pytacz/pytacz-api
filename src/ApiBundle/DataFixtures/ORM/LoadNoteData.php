<?php

namespace ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use ApiBundle\Entity\Note;

class LoadNoteData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
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
     * Load note into test database
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $note = new Note();
        $note->setName('TestNote');
        $note->setContent('test content');
        $note->setAskable(true);
        $note->setNotebook($this->getReference('notebook'));

        $manager->persist($note);
        $manager->flush();

        $this->addReference('note', $note);
    }

    public function getDependencies()
    {
        return [
            LoadUserData::class,
            LoadNotebookData::class
        ];
    }
}

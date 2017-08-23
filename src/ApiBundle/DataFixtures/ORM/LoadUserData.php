<?php

namespace ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use ApiBundle\Entity\User;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
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
     * Load user into test database
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('TestUser');
        $user->setEmail('test@foo.com');
        $password = $this->container->get('security.password_encoder')->encodePassword($user, 'testpassword');
        $user->setPassword($password);
        $user->setIsActive(true);
        $user->setRegisterIp('127.0.0.1');
        $user->setRegisterDate(new \DateTime());

        $manager->persist($user);
        $manager->flush();
    }
}
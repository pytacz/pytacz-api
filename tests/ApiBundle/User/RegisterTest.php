<?php

namespace Tests\ApiBundle\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ApiBundle\Entity\User;
use ApiBundle\User\Register;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectRepository;

class RegisterTest extends WebTestCase
{
    public function testActivateUserSuccess()
    {
        $user = new User();
        $user->setRegisterHash('b59db6a2887a9fa0f3aa5497e660ba');
        $user->setIsActive(false);

        $userRepository = $this->createMock(ObjectRepository::class);

        $userRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($user);

        $em = $this->createMock(EntityManager::class);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($userRepository);

        $register = new Register($em, null, null);
        $data = $register->activateUser('b59db6a2887a9fa0f3aa5497e660ba');
        $this->assertTrue($data['success']);
    }

    public function testActivateUserFail()
    {
        $userRepository = $this->createMock(ObjectRepository::class);

        $userRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);

        $em = $this->createMock(EntityManager::class);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($userRepository);

        $register = new Register($em, null, null);
        $data = $register->activateUser('failhash');
        $this->assertFalse($data['success']);
    }
}
<?php

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UsersControllerTest extends WebTestCase
{
    public function testPostUsersActionSuccess()
    {
        $client = $this->createClient();
        $client->request('POST', '/users', [
            'register' => [
                'username' => 'example',
                'email' => 'example@waluk.pl',
                'password' => [
                    'first' => 'example1234',
                    'second' => 'example1234'
                ]
            ]
        ]);
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($data['success']);
    }

    public function testPostUsersActionFail()
    {
        $client = $this->createClient();
        $client->request('POST', '/users');
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertSame(
            'Formularz nie został przesłany prawidłowo',
            $data['errors']['request']
        );
    }
}
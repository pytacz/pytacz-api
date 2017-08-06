<?php

namespace Tests\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UsersControllerTest extends WebTestCase
{
    public function testPostUsersActionSuccess()
    {
        $client = $this->createClient();
        $client->enableProfiler();

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

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals('Potwierdzenie rejestracji | pytacz.pl', $message->getSubject());
        $this->assertEquals($client->getKernel()->getContainer()->getParameter('mailer_user'), key($message->getFrom()));

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

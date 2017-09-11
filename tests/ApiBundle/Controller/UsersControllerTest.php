<?php

namespace Tests\ApiBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class UsersControllerTest extends WebTestCase
{
    /**
     * Create a client with Authorization header
     *
     * @param string $username
     * @param string $password
     *
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function createAuthenticatedClient($username = 'TestUser', $password = 'testpassword')
    {
        $this->loadFixtures([
            'ApiBundle\DataFixtures\ORM\LoadUserData',
        ]);

        $client = static::makeClient();
        $client->request('POST', '/auth/login', [
            '_username' => $username,
            '_password' => $password
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);

        $client = static::makeClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    public function testGetAuthUserAction()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/auth/user');

        $this->assertStatusCode(200, $client);
    }

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
        /** @var \Swift_Message $message */
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

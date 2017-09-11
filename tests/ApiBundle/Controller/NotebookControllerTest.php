<?php

namespace Tests\ApiBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class NotebookControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->loadFixtures([
            'ApiBundle\DataFixtures\ORM\LoadUserData',
        ]);
    }
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

    public function testPostNotebookActionSuccess()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('POST', '/notebooks', [
            'notebook' => [
                'name' => 'Test notebook name',
                'private' => 'true'
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
    }

    public function testPostNotebookActionFailure()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('POST', '/notebooks', [
            'notebook' => [
                'name' => 'xx',
                'private' => 'true'
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
    }
}

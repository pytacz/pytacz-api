<?php

namespace Tests\ApiBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->loadFixtures([
            'ApiBundle\DataFixtures\ORM\LoadUserData',
        ]);
    }

    public function testLoginActionSuccess()
    {
        $client = static::makeClient();

        $client->request('POST', '/auth/login', [
            '_username' => 'TestUser',
            '_password' => 'testpassword'
        ]);

        $this->assertStatusCode(200, $client);
    }

    public function testLoginActionFailureGetUser()
    {
        $client = static::makeClient();

        $client->request('POST', '/auth/login', [
            '_username' => 'badusername',
            '_password' => 'testpassword'
        ]);

        $this->assertStatusCode(401, $client);
    }

    public function testLoginActionFailureCheckCredentials()
    {
        $client = static::makeClient();

        $client->request('POST', '/auth/login', [
            '_username' => 'TestUser',
            '_password' => 'badpassword'
        ]);

        $this->assertStatusCode(401, $client);
    }

    /**
     * Create a client with valid Authorization header
     *
     * @param string $username
     * @param string $password
     *
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function createValidAuthenticatedClient($username = 'TestUser', $password = 'testpassword')
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

    /**
     * Create a client with invalid Authorization header
     *
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function createInvalidAuthenticatedClient()
    {
        $client = static::makeClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', 'invalidtoken'));

        return $client;
    }

    public function testRefreshTokenActionSuccess()
    {
        $client = $this->createValidAuthenticatedClient();

        $client->request('POST', '/auth/refresh');

        $this->assertStatusCode(200, $client);
    }

    public function testRefreshTokenActionFailure()
    {
        $client = $this->createInvalidAuthenticatedClient();
        $client->request('POST', '/auth/refresh');

        $this->assertStatusCode(401, $client);
    }
}

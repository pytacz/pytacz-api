<?php

namespace Tests\ApiBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginActionSuccess()
    {
        $this->loadFixtures([
            'ApiBundle\DataFixtures\ORM\LoadUserData',
        ]);

        $client = $this->makeClient();

        $client->request('POST', '/login', [
            '_username' => 'TestUser',
            '_password' => 'testpassword'
        ]);

        $this->assertStatusCode(200, $client);
    }

    public function testLoginActionFailureGetUser()
    {
        $this->loadFixtures([
            'ApiBundle\DataFixtures\ORM\LoadUserData',
        ]);

        $client = $this->makeClient();

        $client->request('POST', '/login', [
            '_username' => 'badusername',
            '_password' => 'testpassword'
        ]);

        $this->assertStatusCode(403, $client);
    }

    public function testLoginActionFailureCheckCredentials()
    {
        $this->loadFixtures([
            'ApiBundle\DataFixtures\ORM\LoadUserData',
        ]);

        $client = $this->makeClient();

        $client->request('POST', '/login', [
            '_username' => 'TestUser',
            '_password' => 'badpassword'
        ]);

        $this->assertStatusCode(403, $client);
    }
}
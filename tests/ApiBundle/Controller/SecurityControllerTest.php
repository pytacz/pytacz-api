<?php

namespace Tests\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginActionFail()
    {
        $client = $this->createClient();
        $client->enableProfiler();

        $client->request('POST', '/login', [
                '_username' => 'TestUser',
                '_password' => 'testpassword',
                '_remember_me' => true
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertFalse($data['success']);
    }
}

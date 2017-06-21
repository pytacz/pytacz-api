<?php

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/index');
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertContains('ok', $data['test']);
    }
}

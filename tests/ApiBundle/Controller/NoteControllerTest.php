<?php

namespace Tests\ApiBundle\Controller;

use ApiBundle\Entity\Notebook;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class NoteControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->loadFixtures([
            'ApiBundle\DataFixtures\ORM\LoadUserData',
            'ApiBundle\DataFixtures\ORM\LoadNotebookData',
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

    /**
     * Getting doctrine
     *
     * @return EntityManager
     */
    protected function getDoctrine()
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();
        return $container->get('doctrine')->getManager();
    }

    /**
     * Get object from test fixture
     *
     * @return object
     */
    protected function getFixtureNotebook()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine();
        return $em->getRepository('ApiBundle:Notebook')
            ->findOneBy(['name' => 'TestNotebook']);
    }

    public function testPostNotesActionSuccess()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Notebook $notebook */
        $notebook = $this->getFixtureNotebook();

        $client->request('POST', '/notes', [
            'note' => [
                'content' => json_encode([
                    'testLabel' => [
                        'subLabel' => 'subText'
                    ]
                ]),
                'id_notebook' => $notebook->getId()
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
    }

    public function testPostNotesActionFailure()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Notebook $notebook */
        $notebook = $this->getFixtureNotebook();

        $client->request('POST', '/notes', [
            'note' => [
                'content' => 'not json',
                'id_notebook' => $notebook->getId()
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
    }
}

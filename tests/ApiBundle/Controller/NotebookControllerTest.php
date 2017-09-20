<?php

namespace Tests\ApiBundle\Controller;

use ApiBundle\Entity\Notebook;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class NotebookControllerTest extends WebTestCase
{
    protected function setUp()
    {
        //load all 4 for testing cascade delete
        $this->loadFixtures([
            'ApiBundle\DataFixtures\ORM\LoadUserData',
            'ApiBundle\DataFixtures\ORM\LoadNotebookData',
            'ApiBundle\DataFixtures\ORM\LoadNoteData',
            'ApiBundle\DataFixtures\ORM\LoadSubNoteData',
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

    public function testNotebookNameUniqueFailure()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('POST', '/notebooks', [
            'notebook' => [
                'name' => 'Good name',
                'private' => 'true'
            ]
        ]);

        //to post notebook with the same name
        $client->request('POST', '/notebooks', [
            'notebook' => [
                'name' => 'Good name',
                'private' => 'true'
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
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

    public function testPatchNotebooksActionSuccess()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Notebook $notebook */
        $notebook = $this->getFixtureNotebook();

        $client->request('PATCH', sprintf('/notebooks/%d', $notebook->getId()), [
            'notebook' => [
                'private' => 'true'
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
    }

    public function testPatchNotebooksActionFailure()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Notebook $notebook */

        $client->request('PATCH', sprintf('/notebooks/%d', 'wrong_id'), [
            'notebook' => [
                'private' => 'true'
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
    }
    
    public function testGetNotebookActionSuccess()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Notebook $notebook */
        $notebook = $this->getFixtureNotebook();

        $client->request('GET', sprintf('/notebooks/%d', $notebook->getId()));

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
        $this->assertEquals($notebook->getId(), $data['notebook'][0]['id']);
    }

    public function testGetNotebookActionFailure()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', sprintf('/notebooks/%s', 'wrong')); //not numeric

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
    }

    public function testGetNotebookActionFailure404()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', sprintf('/notebooks/%d', 99999)); //definitely wrong

        $this->assertStatusCode(404, $client);
    }

    public function testGetNotebooksActionSuccess()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', sprintf('/notebooks?username=%s', 'TestUser'));

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
    }

    public function testGetNotebooksActionFailure()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', sprintf('/notebooks?username=%s', 'non existing'));

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
    }

    public function testDeleteNotebookActionSuccess()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Notebook $notebook */
        $notebook = $this->getFixtureNotebook();

        $client->request('DELETE', sprintf('/notebooks/%s', $notebook->getId()));

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
    }

    public function testDeleteNotebookActionFailure()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('DELETE', sprintf('/notebooks/%s', 'wrong id'));

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
    }
}

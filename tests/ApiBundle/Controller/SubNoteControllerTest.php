<?php

namespace Tests\ApiBundle\Controller;

use ApiBundle\Entity\Notebook;
use ApiBundle\Entity\Note;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class SubNoteControllerTest extends WebTestCase
{
    protected function setUp()
    {
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
     * Get notebook object from test fixture
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

    /**
     * Get note object from test fixture
     *
     * @return object
     */
    protected function getFixtureNote()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine();
        return $em->getRepository('ApiBundle:Note')
            ->findOneBy(['name' => 'TestNote']);
    }

    /**
     * Get subNote object from test fixture
     *
     * @return object
     */
    protected function getFixtureSubNote()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine();
        return $em->getRepository('ApiBundle:SubNote')
            ->findOneBy(['name' => 'TestSubNote']);
    }

    public function testPostSubnotesAction()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Note $note */
        $note = $this->getFixtureNote();

        $client->request('POST', '/subnotes', [
            'sub_note' => [
                'name' => 'RandomName',
                'content' => 'blablabla',
                'askable' => 'false',
                'id_note' => $note->getId()
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
    }

    public function testPostSubnotesActionFailure()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Notebook $notebook */

        $client->request('POST', '/subnotes', [
            'sub_note' => [
                'content' => 'blablabla',
                'id_note' => 'wrong'
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
    }

    public function testPatchSubnotesActionSuccess()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Note $note */
        $note = $this->getFixtureNote();

        $client->request('PATCH', sprintf('/subnotes/%d', $note->getId()), [
            'sub_note' => [
                'name' => 'testName',
                'content' => 'blablabla',
                'askable' => false
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
    }

    public function testPatchSubnotesActionFailure()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('PATCH', sprintf('/subnotes/%d', 'wrong'), [
            'sub_note' => [
                'content' => 'blablabla'
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
    }

    public function testGetSubnoteActionSuccess()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Note $note */
        $subNote = $this->getFixtureSubNote();

        $client->request('GET', sprintf('/subnotes/%d', $subNote->getId()));

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
        $this->assertEquals($subNote->getId(), $data['sub_note'][0]['id']);
    }

    public function testGetSubnoteActionFailure()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', sprintf('/subnotes/%s', 'wrong')); //not numeric

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
    }

    public function testGetSubnoteActionFailure404()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', sprintf('/subnotes/%d', 9999)); //definitely wrong

        $this->assertStatusCode(404, $client);
    }

    public function testDeleteSubnoteActionSuccess()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Note $note */
        $subNote = $this->getFixtureSubNote();

        $client->request('DELETE', sprintf('/subnotes/%d', $subNote->getId()));

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
    }

    public function testDeleteSubnoteActionFailure()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('DELETE', sprintf('/subnotes/%d', 'wrong'));

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
    }
}

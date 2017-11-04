<?php

namespace Tests\ApiBundle\Controller;

use ApiBundle\Entity\Notebook;
use ApiBundle\Entity\Note;
use ApiBundle\Entity\SubNote;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class NoteControllerTest extends WebTestCase
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

    public function testPostNotesActionSuccess()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Notebook $notebook */
        $notebook = $this->getFixtureNotebook();

        $client->request('POST', '/notes', [
            'note' => [
                'name' => 'RandomName',
                'content' => 'blablabla',
                'askable' => 'false',
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

        $client->request('POST', '/notes', [
            'note' => [
                'content' => 'blablabla',
                'id_notebook' => 'wrong'
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
    }

    public function testPatchNotesActionSuccess()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Note $note */
        $note = $this->getFixtureNote();

        $client->request('PATCH', sprintf('/notes/%d', $note->getId()), [
            'note' => [
                'name' => 'testName',
                'content' => 'blablabla',
                'askable' => true
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
    }

    public function testPatchNotesActionFailure()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('PATCH', sprintf('/notes/%d', 'wrong'), [
            'note' => [
                'content' => 'blablabla'
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
    }

    public function testGetNoteActionSuccess()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Note $note */
        $note = $this->getFixtureNote();

        $client->request('GET', sprintf('/notes/%d', $note->getId()));

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
        $this->assertEquals($note->getId(), $data['note'][0]['id']);
    }

    public function testGetNoteActionFailure()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', sprintf('/notes/%s', 'wrong')); //not numeric

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
    }

    public function testGetNoteActionFailure404()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', sprintf('/notes/%d', 9999)); //definitely wrong

        $this->assertStatusCode(404, $client);
    }

    public function testDeleteNoteActionSuccess()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Note $note */
        $note = $this->getFixtureNote();

        $client->request('DELETE', sprintf('/notes/%d', $note->getId()));

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
    }

    public function testDeleteNoteActionFailure()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('DELETE', sprintf('/notes/%d', 'wrong'));

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
    }

    public function testGetNoteSubnotesActionSuccess()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Note $note */
        $note = $this->getFixtureNote();

        $client->request('GET', sprintf('/notes/%d/subnotes', $note->getId()));

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
    }

    public function testGetNoteSubnotesActionFailure()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', sprintf('/notes/%d/subnotes', 'wrong'));

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
    }

    public function testPostNoteAnswersActionSuccess()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Note $note */
        $note = $this->getFixtureNote();
        /** @var SubNote $subNote */
        $subNote = $this->getFixtureSubNote();

        $client->request('POST', sprintf('/notes/%d/answers', $note->getId()), [
            'answer' => [
                'answer' => 'test content',
                'sub_notes' => [
                    0 => [
                        'id' => $subNote->getId(),
                        'answer' => 'test content'
                    ]
                ],
                'reverse' => false
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
        $this->assertTrue($data['correct']);

        //reverse
        $client->request('POST', sprintf('/notes/%d/answers', $note->getId()), [
            'answer' => [
                'answer' => 'TestNote',
                'sub_notes' => [
                    0 => [
                        'id' => $subNote->getId(),
                        'answer' => 'TestSubNote'
                    ]
                ],
                'reverse' => true
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
    }

    public function testPostNoteAnswersActionFailure()
    {
        $client = $this->createAuthenticatedClient();

        $client->request('POST', sprintf('/notes/%d/answers', 'wrong_id'));

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertFalse($data['success']);
    }

    public function testPostNoteAnswersActionIncorrect()
    {
        $client = $this->createAuthenticatedClient();
        /** @var Note $note */
        $note = $this->getFixtureNote();
        /** @var SubNote $subNote */
        $subNote = $this->getFixtureSubNote();

        $client->request('POST', sprintf('/notes/%d/answers', $note->getId()), [
            'answer' => [
                'answer' => 'bad answer',
                'sub_notes' => [
                    0 => [
                        'id' => $subNote->getId(),
                        'answer' => 'bad answer'
                    ]
                ],
                'reverse' => false
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
        $this->assertFalse($data['correct']);
        $this->assertEquals($data['answer']['id'], $note->getId());
        $this->assertEquals($data['answer']['sub_notes'][0]['id'], $subNote->getId());
        $this->assertEquals($data['answer']['field'], $note->getName());
        $this->assertEquals($data['answer']['answer'], $note->getContent());
        $this->assertEquals($data['answer']['sub_notes'][0]['field'], $subNote->getName());
        $this->assertEquals($data['answer']['sub_notes'][0]['answer'], $subNote->getContent());

        //reverse
        $client->request('POST', sprintf('/notes/%d/answers', $note->getId()), [
            'answer' => [
                'answer' => 'bad answer',
                'sub_notes' => [
                    0 => [
                        'id' => $subNote->getId(),
                        'answer' => 'bad answer'
                    ]
                ],
                'reverse' => true
            ]
        ]);

        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertStatusCode(200, $client);
        $this->assertTrue($data['success']);
        $this->assertFalse($data['correct']);
        $this->assertEquals($data['answer']['id'], $note->getId());
        $this->assertEquals($data['answer']['sub_notes'][0]['id'], $subNote->getId());
        $this->assertEquals($data['answer']['field'], $note->getContent());
        $this->assertEquals($data['answer']['answer'], $note->getName());
        $this->assertEquals($data['answer']['sub_notes'][0]['field'], $subNote->getContent());
        $this->assertEquals($data['answer']['sub_notes'][0]['answer'], $subNote->getName());
    }
}

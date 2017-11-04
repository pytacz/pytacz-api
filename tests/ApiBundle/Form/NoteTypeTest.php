<?php

namespace Tests\ApiBundle\Form;

use ApiBundle\Form\NoteType;
use ApiBundle\Entity\Note;
use Symfony\Component\Form\Test\TypeTestCase;

class NoteTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'name' => 'testName',
            'content' => 'blablabla',
            'askable' => true
        ];

        $form = $this->factory->create(NoteType::class);

        $note = new Note();
        $note
            ->setName($formData['name'])
            ->setContent($formData['content'])
            ->setAskable($formData['askable']);
        $noteData = [
            'name' => $note->getName(),
            'content' => $note->getContent(),
            'askable' => $note->getAskable()
        ];

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($noteData, $form->getData());
    }
}

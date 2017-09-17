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
            'content' => json_encode([
                'testLabel' => 'testContent'
            ])
        ];

        $form = $this->factory->create(NoteType::class);

        $note = new Note();
        $note->setContent($formData['content']);
        $noteData = [
            'content' => $note->getContent()
        ];

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($noteData, $form->getData());
    }
}

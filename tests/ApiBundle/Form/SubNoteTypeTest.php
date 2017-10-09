<?php

namespace Tests\ApiBundle\Form;

use ApiBundle\Form\SubNoteType;
use ApiBundle\Entity\SubNote;
use Symfony\Component\Form\Test\TypeTestCase;

class SubNoteTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'name' => 'testName',
            'content' => 'blablabla',
            'askable' => true,
            'note' => 1
        ];

        $form = $this->factory->create(SubNoteType::class);

        $subNote = new SubNote();
        $subNote
            ->setName($formData['name'])
            ->setContent($formData['content'])
            ->setAskable($formData['askable']);
        $subNoteData = [
            'name' => $subNote->getName(),
            'content' => $subNote->getContent(),
            'askable' => $subNote->getAskable(),
            'note' => 1
        ];

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($subNoteData, $form->getData());
    }
}

<?php

namespace Tests\ApiBundle\Form;

use ApiBundle\Form\NotebookType;
use ApiBundle\Entity\Notebook;
use Symfony\Component\Form\Test\TypeTestCase;

class NotebookTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = [
            'name' => 'testName',
            'private' => true,
        ];

        $form = $this->factory->create(NotebookType::class);

        $notebook = new Notebook();
        $notebook->setName($formData['name']);
        $notebook->setPrivate($formData['private']);
        $notebookData = [
            'name' => $notebook->getName(),
            'private' => $notebook->getPrivate()
        ];

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($notebookData, $form->getData());
    }
}

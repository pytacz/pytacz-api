<?php

namespace ApiBundle\Service;

use Symfony\Component\Form\Form;

class FormErrors
{
    public function getAll(Form $form): array
    {
        $errors = [];
        foreach ($form as $formElement) {
            $elementName = $formElement->getName();
            foreach ($formElement->getErrors(true) as $error) {
                $errors[$elementName] = $error->getMessage();
            }
        }
        return $errors;
    }
}

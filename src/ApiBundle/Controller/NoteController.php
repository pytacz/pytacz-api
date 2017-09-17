<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;

class NoteController extends FOSRestController
{
    public function postNotesAction(Request $request)
    {
        $data = $this->get('api.note_manager')->createNote($request);

        if ($data['success']) {
            return $data;
        } else {
            if (isset($data['form'])) {
                /** @var Form $form */
                $form = $data['form'];
                $errors = $this->get('api.form_errors')->getAll($form);
                if (empty($errors)) {
                    $errors['request'] = 'Formularz nie został przesłany prawidłowo';
                }
            } else {
                $errors['request'] = 'Wystąpił błąd';
            }
            return [
                'success' => false,
                'errors' => $errors
            ];
        }
    }
}

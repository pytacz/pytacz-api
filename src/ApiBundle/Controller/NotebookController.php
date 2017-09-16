<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;

class NotebookController extends FOSRestController
{
    public function postNotebooksAction(Request $request): array
    {
        $data = $this->get('api.notebook_manager')->createNotebook($request);

        if ($data['success']) {
            return $data;
        } else {
            /** @var Form $form */
            $form = $data['form'];
            $errors = $this->get('api.form_errors')->getAll($form);
            if (empty($errors)) {
                $errors['request'] = 'Formularz nie został przesłany prawidłowo';
            }
            return [
                'success' => false,
                'errors' => $errors
            ];
        }
    }

    public function getNotebookAction($id): array
    {
        $data = $this->get('api.notebook_manager')->getNotebook($id);
        return $data;
    }

    public function getNotebooksAction(Request $request)
    {
        $data = $this->get('api.notebook_manager')->getNotebooks($request);
        return $data;
    }

    public function patchNotebooksAction(Request $request): array
    {
        $data = $this->get('api.notebook_manager')->patchNotebook($request);

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
                $errors['request'] = 'Formularz nie został przesłany prawidłowo';
            }
            return [
                'success' => false,
                'errors' => $errors
            ];
        }
    }

    public function deleteNotebookAction($id): array
    {
        $data = $this->get('api.notebook_manager')->removeNotebook($id);
        return $data;
    }
}

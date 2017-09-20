<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotebookController extends FOSRestController
{
    public function postNotebooksAction(Request $request)
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

    public function getNotebookAction($id)
    {
        $data = $this->get('api.notebook_manager')->getNotebook($id);
        if (isset($data['code'])) {
            if ($data['code'] == 403) {
                throw new AccessDeniedHttpException();
            } elseif ($data['code'] == 404) {
                throw new NotFoundHttpException();
            }
        }
        return $data;
    }

    public function getNotebooksAction(Request $request)
    {
        $data = $this->get('api.notebook_manager')->getNotebooks($request);
        return $data;
    }

    public function patchNotebooksAction(Request $request, $id)
    {
        $data = $this->get('api.notebook_manager')->patchNotebook($request, $id);

        if ($data['success']) {
            return $data;
        } else {
            if (isset($data['code'])) {
                if ($data['code'] == 403) {
                    throw new AccessDeniedHttpException();
                }
            }
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

    public function deleteNotebookAction($id)
    {
        $data = $this->get('api.notebook_manager')->removeNotebook($id);
        if (isset($data['code'])) {
            if ($data['code'] == 403) {
                throw new AccessDeniedHttpException();
            }
        }
        return $data;
    }

    public function getNotebookNotesAction($id)
    {
        $data = $this->get('api.note_manager')->getNotes($id);
        if (isset($data['code'])) {
            if ($data['code'] == 403) {
                throw new AccessDeniedHttpException();
            }
        }
        return $data;
    }
}

<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubNoteController extends FOSRestController
{
    public function postSubnotesAction(Request $request)
    {
        $data = $this->get('api.sub_note_manager')->createSubNote($request);

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

    public function patchSubnotesAction(Request $request, $id)
    {
        $data = $this->get('api.sub_note_manager')->patchSubNote($request, $id);

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
                $errors['request'] = 'Wystąpił błąd';
            }
            return [
                'success' => false,
                'errors' => $errors
            ];
        }
    }

    public function getSubnoteAction($id)
    {
        $data = $this->get('api.sub_note_manager')->getSubNote($id);
        if (isset($data['code'])) {
            if ($data['code'] == 403) {
                throw new AccessDeniedHttpException();
            } elseif ($data['code'] == 404) {
                throw new NotFoundHttpException();
            }
        }
        return $data;
    }

    public function deleteSubnoteAction($id)
    {
        $data = $this->get('api.sub_note_manager')->removeSubNote($id);
        if (isset($data['code'])) {
            if ($data['code'] == 403) {
                throw new AccessDeniedHttpException();
            }
        }
        return $data;
    }
}

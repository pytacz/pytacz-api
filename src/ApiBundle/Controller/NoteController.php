<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
                $errors['request'] = 'Formularz nie został przesłany prawidłowo';
            }
            return [
                'success' => false,
                'errors' => $errors
            ];
        }
    }

    public function patchNotesAction(Request $request, $id)
    {
        $data = $this->get('api.note_manager')->patchNote($request, $id);

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

    public function getNoteAction($id)
    {
        $data = $this->get('api.note_manager')->getNote($id);
        if (isset($data['code'])) {
            if ($data['code'] == 403) {
                throw new AccessDeniedHttpException();
            } elseif ($data['code'] == 404) {
                throw new NotFoundHttpException();
            }
        }
        return $data;
    }

    public function deleteNoteAction($id)
    {
        $data = $this->get('api.note_manager')->removeNote($id);
        if (isset($data['code'])) {
            if ($data['code'] == 403) {
                throw new AccessDeniedHttpException();
            }
        }
        return $data;
    }

    public function getNoteSubnotesAction($id)
    {
        $data = $this->get('api.sub_note_manager')->getSubNotes($id);
        if (isset($data['code'])) {
            if ($data['code'] == 403) {
                throw new AccessDeniedHttpException();
            }
        }
        return $data;
    }

    public function postNoteAnswersAction(Request $request, $id)
    {
        $data = $this->get('api.answer_manager')->postAnswer($request, $id);
        if (isset($data['code'])) {
            if ($data['code'] == 403) {
                throw new AccessDeniedHttpException();
            }
        }
        return $data;
    }
}

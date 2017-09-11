<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class NotebookController extends FOSRestController
{
    public function postNotebooksAction(Request $request): array
    {
        /** @var User $user */
        $user = $this->getUser();

        $data = $this->get('api.notebook_manager')->createNotebook($request, $user);

        if ($data['success']) {
            return $data;
        } else {
            $errors = $this->get('api.form_errors')->getAll($data['form']);
            if (empty($errors)) {
                $errors['request'] = 'Formularz nie został przesłany prawidłowo';
            }
            return [
                'success' => false,
                'errors' => $errors
            ];
        }
    }
}

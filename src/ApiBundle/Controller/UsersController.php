<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends FOSRestController
{
    public function postUsersAction(Request $request): array
    {
        $data = $this->get('api.registration')->registerUser($request);

        if ($data['success']) {
            /** @var User $user */
            $user = $data['user'];
            $this->get('api.email')->sendEmail(
                'Potwierdzenie rejestracji | pytacz.pl',
                'registrationConfirmation',
                $user->getEmail(),
                [
                    'hash' => $user->getRegisterHash(),
                    'username' => $user->getUsername()
                ]
            );
            return ['success' => true];
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

    public function patchUsersActivateAction(string $slug): array
    {
        return $this->get('api.registration')->activateUser($slug);
    }
}

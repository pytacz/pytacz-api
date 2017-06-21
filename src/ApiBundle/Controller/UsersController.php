<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\User;
use ApiBundle\Form\RegisterType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends FOSRestController
{
    public function postUsersAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setIsActive(true);
            $user->setRegisterIp($request->getClientIp());
            $user->setRegisterDate(new \DateTime());
            $user->setRegisterHash(bin2hex(random_bytes(15)));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return ['success' => true];
        }

        $errors = $this->get('api.form_errors')->getAll($form);
        return [
            'success' => false,
            'errors' => $errors
        ];
    }
}
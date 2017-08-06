<?php

namespace ApiBundle\User;

use ApiBundle\Entity\User;
use ApiBundle\Form\RegisterType;

class Register
{
    private $em;
    private $formFactory;
    private $passwordEncoder;

    public function __construct($em, $formFactory, $passwordEncoder)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function registerUser($request)
    {
        $user = new User();
        $form = $this->formFactory->create(RegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setIsActive(false);
            $user->setRegisterIp($request->getClientIp());
            $user->setRegisterDate(new \DateTime());
            $user->setRegisterHash(bin2hex(random_bytes(15)));

            $this->em->persist($user);
            $this->em->flush();

            return ['user' => $user, 'success' => true];
        }

        return ['form' => $form, 'success' => false];
    }

    public function activateUser($slug)
    {
        $repository = $this->em->getRepository(User::class);

        $user = $repository->findOneBy(['registerHash' => $slug]);

        if (!$user) {
            return ['success' => false];
        }

        $user->setRegisterHash(null);
        $user->setIsActive(true);

        return ['success' => true];
    }
}

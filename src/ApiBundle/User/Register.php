<?php

namespace ApiBundle\User;

use ApiBundle\Entity\User;
use ApiBundle\Form\RegisterType;
use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class Register
{
    /** @var EntityManager $em */
    private $em;
    /** @var UserPasswordEncoder $passwordEncoder */
    private $passwordEncoder;
    /** @var FormFactory $formFactory */
    private $formFactory;

    public function __construct(EntityManager $em, $formFactory, $passwordEncoder)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function registerUser(Request $request): array
    {
        $user = new User();
        /** @var Form $form */
        $form = $this->formFactory->create(RegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setIsActive(false);
            $user->setRegisterIp($request->getClientIp());
            $user->setRegisterDate(new DateTime());
            $user->setRegisterHash(bin2hex(random_bytes(15)));

            $this->em->persist($user);
            $this->em->flush();

            return ['user' => $user, 'success' => true];
        }

        return ['form' => $form, 'success' => false];
    }

    public function activateUser(string $slug): array
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

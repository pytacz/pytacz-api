<?php

namespace ApiBundle\Notes;

use ApiBundle\Entity\User;
use ApiBundle\Entity\Notebook;
use Doctrine\ORM\EntityManager;
use ApiBundle\Form\NotebookType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;

class NotebookManager
{
    /** @var EntityManager $em */
    private $em;
    /** @var FormFactory $formFactory */
    private $formFactory;

    public function __construct(EntityManager $em, FormFactory $formFactory)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
    }

    public function createNotebook(Request $request, User $user)
    {
        $notebook = new Notebook();
        /** @var Form $form */
        $form = $this->formFactory->create(NotebookType::class, $notebook);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $notebook->setUser($user);

            $this->em->persist($notebook);
            $this->em->flush();

            return ['success' => true];
        }

        return ['form' => $form, 'success' => false];
    }
}
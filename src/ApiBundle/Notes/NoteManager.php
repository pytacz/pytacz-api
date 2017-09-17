<?php

namespace ApiBundle\Notes;

use ApiBundle\Entity\User;
use ApiBundle\Entity\Note;
use Doctrine\ORM\EntityManager;
use ApiBundle\Form\NoteType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class NoteManager
{
    /** @var EntityManager $em */
    private $em;
    /** @var FormFactory $formFactory */
    private $formFactory;
    /** @var TokenStorage $tokenStorage */
    private $tokenStorage;

    public function __construct(EntityManager $em, FormFactory $formFactory, TokenStorage $tokenStorage)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->tokenStorage = $tokenStorage;
    }

    public function createNote(Request $request)
    {
        if (isset($request->request->get('note')['id_notebook'])) {
            /** @var User $user */
            $user = $this->tokenStorage->getToken()->getUser();
            $notebook = $this->em->getRepository('ApiBundle:Notebook')
                ->findOneBy(['id' => $request->request->get('note')['id_notebook'], 'user' => $user]);

            if ($notebook) {
                if (isset($request->request->get('note')['content'])) {
                    $note = new Note();
                    /** @var Form $form */
                    $form = $this->formFactory->create(NoteType::class, $note);
                    $request->request->set('note', [
                        'content' => $request->request->get('note')['content']
                    ]);

                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {
                        $note->setNotebook($notebook);
                        $this->em->persist($note);
                        $this->em->flush();
                        return ['success' => true];
                    }

                    return ['form' => $form, 'success' => false];
                }
                return ['success' => false];
            }
            return ['success' => false];
        }
        return ['success' => false];
    }
}

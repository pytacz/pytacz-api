<?php

namespace ApiBundle\Notes;

use ApiBundle\Entity\Notebook;
use ApiBundle\Entity\Note;
use ApiBundle\Entity\SubNote;
use Doctrine\ORM\EntityManager;
use ApiBundle\Form\NoteType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class SubNoteManager
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

    /**
     * Create subNote for logged user base on note id from request
     *
     * @param Request $request
     *
     * @return array
     */
    public function createSubNote(Request $request): array
    {
        $body = $request->request->get('subNote');
        if (isset($body['id_note']) && isset($body['name']) && isset($body['content']) && isset($body['askable'])) {
            /** @var Note $note */
            $note = $this->em->getRepository('ApiBundle:Note')
                ->findOneBy(['id' => $body['id_note']]);

            if ($note) {
                if ($note->getNotebook()->getUser() == $this->tokenStorage->getToken()->getUser()) {
                    unset($body['id_note']);
                    $subNote = new SubNote();
                    /** @var Form $form */
                    $form = $this->formFactory->create(NoteType::class, $subNote);
                    $request->request->set('note', $body);

                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {
                        $name = preg_replace('/\s+/', ' ', $body['name']);
                        $content = preg_replace('/\s+/', ' ', $body['content']);
                        $subNote
                            ->setName($name)
                            ->setContent($content)
                            ->setNote($note)
                            ->setAskable(filter_var($body['askable'], FILTER_VALIDATE_BOOLEAN));
                        $this->em->persist($subNote);
                        $this->em->flush();
                        return ['success' => true, 'id' => $subNote->getId()];
                    }

                    return ['form' => $form, 'success' => false];
                }
                return ['success' => false];
            }
            return ['success' => false];
        }
        return ['success' => false];
    }

    /**
     * Remove subNote by id
     *
     * @param string    $id
     * @param Request   $request
     *
     * @return array
     */
    public function patchSubNote(Request $request, $id): array
    {
        if (is_numeric($id)) {
            /** @var Note $note */
            $subNote = $this->em->getRepository('ApiBundle:SubNote')
                ->findOneBy(['id' => $id]);

            if ($subNote) {
                $body = $request->get('subNote');
                if ($subNote->getNote()->getNotebook()->getUser() == $this->tokenStorage->getToken()->getUser()) {
                    /** @var Form $form */
                    $form = $this->formFactory
                        ->create(NoteType::class, $subNote, ['method' => $request->getMethod()]);

                    if (isset($body['name'])) {
                        $body['name'] = preg_replace('/\s+/', ' ', $body['name']);
                    }
                    if (isset($body['askable'])) {
                        $body['askable'] = filter_var($body['private'], FILTER_VALIDATE_BOOLEAN);
                    }
                    if (isset($body['content'])) {
                        $body['content'] = preg_replace('/\s+/', ' ', $body['content']);
                    }
                    $request->request->set('note', $body);

                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {
                        $this->em->flush();
                        return ['success' => true];
                    }
                    return ['form' => $form, 'success' => false];
                }
                return ['success' => false, 'code' => 403];
            }
            return ['success' => false];
        }
        return ['success' => false];
    }

    /**
     * Get single subNote by it's id
     *
     * @param string $id
     *
     * @return array
     */
    public function getSubNote($id): array
    {
        if (is_numeric($id)) {
            /** @var Note $note */
            $subNote = $this->em->getRepository('ApiBundle:SubNote')
                ->findOneBy(['id' => $id]);

            if ($subNote) {
                /** @var Notebook $notebook */
                $notebook = $subNote->getNote()->getNotebook();
                if ($notebook->getPrivate() === true) {
                    if ($notebook->getUser() == $this->tokenStorage->getToken()->getUser()) {
                        $subNote = $this->em->getRepository('ApiBundle:SubNote')
                            ->findSubNote($subNote->getId());
                        return [
                            'success' => true,
                            'subNote' => $subNote,
                            'notebook' => $notebook->getId()
                        ];
                    }
                    return ['success' => false, 'code' => 403];
                }
                $subNote = $this->em->getRepository('ApiBundle:SubNote')
                    ->findSubNote($subNote->getId());
                return [
                    'success' => true,
                    'subNote' => $subNote,
                    'notebook' => $notebook->getId()
                ];
            }
            return ['success' => false, 'code' => 404];
        }
        return ['success' => false];
    }

    /**
     * Get all subNotes from note with id from parameter
     *
     * @param string $id
     *
     * @return array
     */
    public function getSubNotes($id): array
    {
        if (is_numeric($id)) {
            /** @var Notebook $notebook */
            $note = $this->em->getRepository('ApiBundle:Note')
                ->findOneBy(['id' => $id]);

            if ($note) {
                $notebook = $note->getNotebook();
                if ($notebook->getPrivate() === true) {
                    if ($notebook->getUser() == $this->tokenStorage->getToken()->getUser()) {
                        $notes = $this->em->getRepository('ApiBundle:Note')
                            ->findAllSubNotes($note);
                        return [
                            'success' => true,
                            'subNotes' => $notes,
                            'note' => $note->getId()
                        ];
                    }
                    return ['success' => false, 'code' => 403];
                }
                $notes = $this->em->getRepository('ApiBundle:Note')
                    ->findAllSubNotes($note);
                return [
                    'success' => true,
                    'subNotes' => $notes,
                    'note' => $note->getId()
                ];
            }
            return ['success' => false];
        }
        return ['success' => false];
    }

    /**
     * Remove subNote by id
     *
     * @param string $id
     *
     * @return array
     */
    public function removeSubNote($id): array
    {
        if (is_numeric($id)) {
            /** @var Note $note */
            $subNote = $this->em->getRepository('ApiBundle:SubNote')
                ->findOneBy(['id' => $id]);

            if ($subNote) {
                if ($subNote->getNote()->getNotebook()->getUser() == $this->tokenStorage->getToken()->getUser()) {
                    $this->em->remove($subNote);
                    $this->em->flush();

                    return ['success' => true];
                }
                return ['success' => false, 'code' => 403];
            }
            return ['success' => false];
        }
        return ['success' => false];
    }
}

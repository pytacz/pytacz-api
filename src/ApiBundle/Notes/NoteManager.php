<?php

namespace ApiBundle\Notes;

use ApiBundle\Entity\User;
use ApiBundle\Entity\Notebook;
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

    /**
     * Create note for logged user base on notebook id from request
     *
     * @param Request $request
     *
     * @return array
     */
    public function createNote(Request $request): array
    {
        $body = $request->request->get('note');
        if (isset($body['id_notebook']) && isset($body['name']) && isset($body['content']) && isset($body['askable'])) {
            /** @var User $user */
            $user = $this->tokenStorage->getToken()->getUser();
            /** @var Notebook $notebook */
            $notebook = $this->em->getRepository('ApiBundle:Notebook')
                ->findOneBy(['id' => $body['id_notebook'], 'user' => $user]);

            if ($notebook) {
                unset($body['id_notebook']);
                $note = new Note();
                /** @var Form $form */
                $form = $this->formFactory->create(NoteType::class, $note);
                $request->request->set('note', $body);

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $name = preg_replace('/\s+/', ' ', $body['name']);
                    $content = preg_replace('/\s+/', ' ', $body['content']);
                    $note
                        ->setName($name)
                        ->setContent($content)
                        ->setNotebook($notebook)
                        ->setAskable(filter_var($body['askable'], FILTER_VALIDATE_BOOLEAN));
                    $this->em->persist($note);
                    $this->em->flush();
                    /** @var Note $note */
                    $note = $this->em->getRepository('ApiBundle:Note')
                        ->findNote($note->getId());
                    $note[0]['subNotes'] = [];
                    return ['success' => true, 'note' => $note];
                }

                return ['form' => $form, 'success' => false];
            }
            return ['success' => false];
        }
        return ['success' => false];
    }

    /**
     * Remove note by id
     *
     * @param string    $id
     * @param Request   $request
     *
     * @return array
     */
    public function patchNote(Request $request, $id): array
    {
        if (is_numeric($id)) {
            /** @var Note $note */
            $note = $this->em->getRepository('ApiBundle:Note')
                ->findOneBy(['id' => $id]);

            if ($note) {
                $body = $request->get('note');
                if ($note->getNotebook()->getUser() == $this->tokenStorage->getToken()->getUser()) {
                    /** @var Form $form */
                    $form = $this->formFactory
                        ->create(NoteType::class, $note, ['method' => $request->getMethod()]);
                    $result = [];

                    if (isset($body['name'])) {
                        $body['name'] = preg_replace('/\s+/', ' ', $body['name']);
                        $result['name'] = $body['name'];
                    }
                    if (isset($body['askable'])) {
                        $body['askable'] = filter_var($body['askable'], FILTER_VALIDATE_BOOLEAN);
                        $result['askable'] = $body['askable'];
                    }
                    if (isset($body['content'])) {
                        $body['content'] = preg_replace('/\s+/', ' ', $body['content']);
                        $result['content'] = $body['content'];
                    }
                    $request->request->set('note', $body);

                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {
                        $this->em->flush();
                        return ['success' => true, 'result' => $result];
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
     * Get single note by it's id
     *
     * @param string $id
     *
     * @return array
     */
    public function getNote($id): array
    {
        if (is_numeric($id)) {
            /** @var Note $note */
            $note = $this->em->getRepository('ApiBundle:Note')
                ->findOneBy(['id' => $id]);

            if ($note) {
                /** @var Notebook $notebook */
                $notebook = $note->getNotebook();
                if ($notebook->getPrivate() === true) {
                    if ($notebook->getUser() == $this->tokenStorage->getToken()->getUser()) {
                        $note = $this->em->getRepository('ApiBundle:Note')
                            ->findNote($note->getId());
                        return [
                            'success' => true,
                            'note' => $note,
                            'notebook' => $notebook->getId()
                        ];
                    }
                    return ['success' => false, 'code' => 403];
                }
                $note = $this->em->getRepository('ApiBundle:Note')
                    ->findNote($note->getId());
                return [
                    'success' => true,
                    'note' => $note,
                    'notebook' => $notebook->getId()
                ];
            }
            return ['success' => false, 'code' => 404];
        }
        return ['success' => false];
    }

    /**
     * Get all notes from notebook with id from parameter
     *
     * @param Request   $request
     * @param string    $id
     *
     * @return array
     */
    public function getNotes($request, $id): array
    {
        if (is_numeric($id)) {
            /** @var Notebook $notebook */
            $notebook = $this->em->getRepository('ApiBundle:Notebook')
                ->findOneBy(['id' => $id]);

            if ($notebook) {
                $startPoint = $request->query->get('start_point');
                if (isset($startPoint)) {
                    if (!is_numeric($startPoint) && $startPoint != null) {
                        return ['success' => false];
                    }
                } else {
                    $startPoint = null;
                }

                $notes = $this->em->getRepository('ApiBundle:Notebook')
                    ->findAllNotes($notebook, $startPoint);
                foreach ($notes as $key => $note) {
                    $subNotes = $this->em->getRepository('ApiBundle:Note')
                        ->findAllSubNotes($note['id']);
                    $notes[$key]['subNotes'] = $subNotes;
                }
                if (count($notes) < 20) {
                    $newStartPoint = null;
                } else {
                    $newStartPoint = end($notes)['id']-1;
                    if ($newStartPoint <= 0) {
                        $newStartPoint = null;
                    }
                }

                if ($notebook->getPrivate() === true) {
                    if ($notebook->getUser() == $this->tokenStorage->getToken()->getUser()) {
                        return [
                            'success' => true,
                            'notes' => $notes,
                            'notebook' => $notebook->getId(),
                            'start_point' => $newStartPoint
                        ];
                    }
                    return ['success' => false, 'code' => 403];
                }

                return [
                    'success' => true,
                    'notes' => $notes,
                    'notebook' => $notebook->getId(),
                    'start_point' => $newStartPoint
                ];
            }
            return ['success' => false];
        }
        return ['success' => false];
    }

    /**
     * Remove note by id
     *
     * @param string $id
     *
     * @return array
     */
    public function removeNote($id): array
    {
        if (is_numeric($id)) {
            /** @var Note $note */
            $note = $this->em->getRepository('ApiBundle:Note')
                ->findOneBy(['id' => $id]);

            if ($note) {
                if ($note->getNotebook()->getUser() == $this->tokenStorage->getToken()->getUser()) {
                    $this->em->remove($note);
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

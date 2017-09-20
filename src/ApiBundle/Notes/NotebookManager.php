<?php

namespace ApiBundle\Notes;

use ApiBundle\Entity\User;
use ApiBundle\Entity\Notebook;
use Doctrine\ORM\EntityManager;
use ApiBundle\Form\NotebookType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class NotebookManager
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
     * Create notebook for logged user
     *
     * @param Request $request
     *
     * @return array
     */
    public function createNotebook(Request $request): array
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $notebook = new Notebook();
        /** @var Form $form */
        $form = $this->formFactory->create(NotebookType::class, $notebook);
        $body = $request->request->get('notebook');
        if (isset($body['private']) && isset($body['name'])) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $name = preg_replace('/\s+/', ' ', $body['name']);
                $notebook->setName($name);
                $notebook->setPrivate(filter_var($body['private'], FILTER_VALIDATE_BOOLEAN));
                $notebook->setUser($user);

                $this->em->persist($notebook);
                $this->em->flush();

                return ['success' => true, 'id' => $notebook->getId()];
            }
        }
        return ['form' => $form, 'success' => false];
    }

    /**
     * Return single notebook based on id
     *
     * @param string $id
     *
     * @return array
     */
    public function getNotebook($id): array
    {
        if (is_numeric($id)) {
            /** @var Notebook $notebook */
            $notebook = $this->em->getRepository('ApiBundle:Notebook')
                ->findOneBy(['id' => $id]);

            if ($notebook) {
                $owner = $notebook->getUser()->getUsername();
                if ($notebook->getPrivate() === true) {
                    if ($notebook->getUser() === $this->tokenStorage->getToken()->getUser()) {
                        /** @var Notebook $notebook */
                        $notebook = $this->em->getRepository('ApiBundle:Notebook')
                            ->getNotebook($notebook->getId());
                        return [
                            'success' => true,
                            'notebook' => $notebook,
                            'user' => $owner
                        ];
                    }
                    return ['success' => false, 'code' => 403];
                } else {
                    /** @var Notebook $notebook */
                    $notebook = $this->em->getRepository('ApiBundle:Notebook')
                        ->getNotebook($notebook->getId());
                    return [
                        'success' => true,
                        'notebook' => $notebook,
                        'user' => $owner
                    ];
                }
            }
            return ['success' => false, 'code' => 404];
        }
        return ['success' => false];
    }

    /**
     * Return users notebooks (logged user or user from parameter)
     *
     * @param Request $request
     *
     * @return array
     */
    public function getNotebooks(Request $request): array
    {
        $parameter = $request->query->get('username');
        $user = $this->em->getRepository('ApiBundle:User')
            ->findOneBy(['username' => $parameter]);
        /** @var User $loggedUser */
        $loggedUser = $this->tokenStorage->getToken()->getUser();

        if (empty($parameter) || $loggedUser->getUsername() == $parameter) {
            $notebooks = $this->em->getRepository('ApiBundle:Notebook')
                ->getAllNotebooks($loggedUser);
            $username = $loggedUser->getUsername();
        } elseif ($user) {
            $notebooks = $this->em->getRepository('ApiBundle:Notebook')
                ->getAllPublicNotebooks($user);
            $username = $user->getUsername();
        } else {
            return ['success' => false];
        }

        return [
            'success' => true,
            'notebooks' => $notebooks,
            'user' => $username

        ];
    }

    /**
     * Update fields of notebook
     *
     * @param Request   $request
     * @param int       $id
     *
     * @return array
     */
    public function patchNotebook(Request $request, $id): array
    {
        if (is_numeric($id)) {
            $notebook = $this->em->getRepository('ApiBundle:Notebook')
                ->findOneBy(['id' => $id]);

            if ($notebook) {
                if ($notebook->getUser() == $this->tokenStorage->getToken()->getUser()) {
                    $body = $request->get('notebook');
                    if (isset($body)) {
                        /** @var Form $form */
                        $form = $this->formFactory
                            ->create(NotebookType::class, $notebook, ['method' => $request->getMethod()]);

                        if (isset($body['name'])) {
                            $body['name'] = preg_replace('/\s+/', ' ', $body['name']);
                        }
                        if (isset($body['private'])) {
                            $body['private'] = filter_var($body['private'], FILTER_VALIDATE_BOOLEAN);
                        }

                        $request->request->set('notebook', $body);

                        $form->handleRequest($request);

                        if ($form->isSubmitted() && $form->isValid()) {
                            $this->em->flush();

                            return ['success' => true];
                        }
                        return ['form' => $form, 'success' => false];
                    }
                    return ['success' => false];
                }
                return ['success' => false, 'code' => 403];
            }
            return ['success' => false];
        }
        return ['success' => false];
    }

    /**
     * Remove notebook by id
     *
     * @param string $id
     *
     * @return array
     */
    public function removeNotebook($id): array
    {
        if (is_numeric($id)) {
            /** @var Notebook $notebook */
            $notebook = $this->em->getRepository('ApiBundle:Notebook')
                ->findOneBy(['id' => $id]);

            if ($notebook) {
                if ($notebook->getUser() == $this->tokenStorage->getToken()->getUser()) {
                    $this->em->remove($notebook);
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

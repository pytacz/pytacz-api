<?php

namespace ApiBundle\Notes\QA;

use ApiBundle\Entity\Note;
use ApiBundle\Entity\Notebook;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Form\FormFactory;
use ApiBundle\Entity\User;

class QuestionManager
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
     * Get question
     *
     * @param Request   $request
     * @param string    $id
     *
     * @return array
     */
    public function getQuestion(Request $request, $id)
    {
        if (is_numeric($id)) {
            /** @var Notebook $notebook */
            $notebook = $this->em->getRepository('ApiBundle:Notebook')
                ->findOneBy(['id' => $id]);

            if ($notebook) {
                /** @var User $user */
                $user = $this->tokenStorage->getToken()->getUser();

                if ($notebook->getPrivate() === true && $user !== $notebook->getUser()) {
                    return ['success' => false, 'code' => 403];
                }

                $limit = 10;

                $reverse = filter_var($request->query->get('reverse'), FILTER_VALIDATE_BOOLEAN);

                $result = $this->em->getRepository('ApiBundle:Notebook')
                    ->getIncorrectNoteQuestions($notebook, $user, $limit, $reverse);

                if (count($result) < $limit) {
                    $amount = $limit - count($result);
                    $correct = $this->em->getRepository('ApiBundle:Notebook')
                        ->getCorrectNoteQuestions($notebook, $user, $amount, $reverse);
                    $result = array_merge($result, $correct);
                }

                $incorrect = $this->em->getRepository('ApiBundle:Notebook')
                    ->getOneIncorrectNoteQuestion($notebook, $user, $reverse);

                $result = array_merge($result, $incorrect);

                $pick = array_rand($result);

                $result[$pick]['sub_notes'] = $this->em->getRepository('ApiBundle:Note')
                    ->findAllAskableSubNotes($result[$pick]['id'], $reverse);

                return ['success' => true, 'result' => $result[$pick]];
            }
        }
        return ['success' => false];
    }
}

<?php

namespace ApiBundle\Notes\QA;

use ApiBundle\Entity\Answer;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Form\FormFactory;
use ApiBundle\Entity\Note;

class AnswerManager
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
     * Add answer to database
     *
     * @param Request   $request
     * @param int       $id
     *
     * @return array
     */
    public function postAnswer(Request $request, $id)
    {
        if (is_numeric($id)) {
            /** @var Note $note */
            $note = $this->em->getRepository('ApiBundle:Note')
                ->findOneBy(['id' => $id]);

            if ($note) {
                $answer = new Answer();

                $body = $request->request->get('answer');

                if ($body['reverse']) {
                    $field = $note->getName();
                    $subField = 'name';
                } else {
                    $field = $note->getContent();
                    $subField = 'content';
                }

                if ($field !== $body['answer'] && $note->getAskable() === true) {
                    $answer->setCorrect(false);
                }

                $subNotes = $this->em->getRepository('ApiBundle:Note')
                    ->findAllAskableSubNotes($note->getId());

                if (isset($body['sub_notes']) && !empty($subNotes)) {
                    foreach ($subNotes as $subNote) {
                        $isFound = false;
                        foreach ($body['sub_notes'] as $reqSubNote) {
                            if ($subNote['id'] == $reqSubNote['id']) {
                                $isFound = true;
                                if ($subNote[$subField] !== $reqSubNote['answer']) {
                                    $answer->setCorrect(false);
                                }
                            }
                        }
                        if (!$isFound) {
                            return ['success' => false, 'result' => 'not every answer'];
                        }
                    }

                    $user = $this->tokenStorage->getToken()->getUser();
                    if ($note->getNotebook()->getPrivate() === true && $user === $note->getNotebook()->getUser()) {
                        return ['succes' => false, 'code' => 403];
                    }

                    //end of validating answer
                    $isCorrect = $answer->getCorrect();
                    if (!isset($isCorrect)) {
                        $answer->setCorrect(true);
                    }
                    $answer
                        ->setNote($note)
                        ->setUser($user);
                    $this->em->persist($answer);
                    $this->em->flush();

                    $this->em->getRepository('ApiBundle:Answer')
                        ->deleteWrongAnswers($user, $note);

                    if ($answer->getCorrect() === true) {
                        return ['success' => true, 'correct' => true];
                    } else {
                        return ['success' => true, 'correct' => false];
                    }
                }
            }
            return ['success' => false];
        }
        return ['success' => false];
    }
}

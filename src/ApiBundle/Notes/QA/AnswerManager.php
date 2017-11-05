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

                if (!isset($body['reverse']) || $body['reverse'] === false) {
                    $answerField = $note->getContent();
                    $field = $note->getName();
                } else {
                    $answerField = $note->getName();
                    $field = $note->getContent();
                }

                if ($answerField !== $body['answer'] && $note->getAskable() === true) {
                    $answer->setCorrect(false);
                }

                if (isset($body['reverse'])) {
                    $body['reverse'] = filter_var($body['reverse'], FILTER_VALIDATE_BOOLEAN);
                } else {
                    $body['reverse'] = false;
                }

                $subNotes = $this->em->getRepository('ApiBundle:Note')
                    ->findAnswersForSubNotes($note->getId(), $body['reverse']);

                if (isset($body['sub_notes']) && !empty($subNotes)) {
                    foreach ($subNotes as $key => $subNote) {
                        $isFound = false;
                        foreach ($body['sub_notes'] as $reqSubNote) {
                            if ($subNote['id'] == $reqSubNote['id']) {
                                $isFound = true;
                                if ($subNote['answer'] !== $reqSubNote['answer']) {
                                    $answer->setCorrect(false);
                                }
                            }
                        }
                        if (!$isFound) {
                            return ['success' => false];
                        }
                    }
                }

                $user = $this->tokenStorage->getToken()->getUser();
                if ($note->getNotebook()->getPrivate() === true && $user !== $note->getNotebook()->getUser()) {
                    return ['success' => false, 'code' => 403];
                }

                //end of validating answer
                $isCorrect = $answer->getCorrect();
                if (!isset($isCorrect)) {
                    $answer->setCorrect(true);
                }
                $this->em->getRepository('ApiBundle:Answer')
                    ->deleteAnswers($user, $note);
                $answer
                    ->setNote($note)
                    ->setUser($user);
                $this->em->persist($answer);
                $this->em->flush();
                
                $answers = [
                    'id' => $note->getId(),
                    'field' => $field,
                    'answer' => $answerField
                ];
                $answers['sub_notes'] = $subNotes;

                if ($answer->getCorrect() === true) {
                    return ['success' => true, 'correct' => true, 'answer' => $answers];
                } else {
                    return ['success' => true, 'correct' => false, 'answer' => $answers];
                }
            }
            return ['success' => false];
        }
        return ['success' => false];
    }
}

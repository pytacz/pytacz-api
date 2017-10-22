<?php

namespace ApiBundle\Repository;

use ApiBundle\Entity\Note;
use ApiBundle\Entity\User;

/**
 * AnswerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AnswerRepository extends \Doctrine\ORM\EntityRepository
{
    public function deleteWrongAnswers(User $user, Note $note)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'DELETE FROM ApiBundle:Answer a
            WHERE a.note = :note AND a.user = :user AND a.correct = false')
            ->setParameters([
                'note' => $note,
                'user' => $user
            ]);
        $query->execute();
    }
}
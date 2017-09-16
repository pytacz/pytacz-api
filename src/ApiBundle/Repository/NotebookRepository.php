<?php

namespace ApiBundle\Repository;

/**
 * NotebookRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotebookRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAllPublicNotebooks($user)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT n.id, n.name, n.private FROM ApiBundle:Notebook n
            WHERE n.user = :user AND n.private = false
            ORDER BY n.id DESC')
            ->setParameters([
                'user' => $user
            ]);
        return $query->getResult();
    }

    public function getAllNotebooks($user)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT n.id, n.name, n.private FROM ApiBundle:Notebook n
            WHERE n.user = :user
            ORDER BY n.id DESC')
            ->setParameters([
                'user' => $user
            ]);
        return $query->getResult();
    }
}

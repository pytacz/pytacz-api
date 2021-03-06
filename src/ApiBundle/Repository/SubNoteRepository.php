<?php

namespace ApiBundle\Repository;

/**
 * SubNoteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SubNoteRepository extends \Doctrine\ORM\EntityRepository
{
    public function findSubNote($id)
    {
        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT n.id, n.name, n.content, n.askable FROM ApiBundle:SubNote n
            WHERE n.id = :id')
            ->setParameters([
                'id' => $id
            ]);
        return $query->getResult();
    }
}

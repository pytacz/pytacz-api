<?php

namespace ApiBundle\Validator\Constraints;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;

class NotebookNameUniqueValidator extends ConstraintValidator
{
    /** @var EntityManager $em */
    private $em;
    /** @var TokenStorage $tokenStorage */
    private $tokenStorage;

    public function __construct(EntityManager $em, TokenStorage $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }
    public function validate($value, Constraint $constraint)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        $notebook = $this->em->getRepository('ApiBundle:Notebook')
            ->findOneBy(['name' => $value, 'user' => $user]);

        if ($notebook) {
            $this->context->buildViolation($constraint->getMessage())
                ->addViolation();
        }
    }
}

<?php

namespace ApiBundle\Validator\Constraints;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;

class NotebooksCounterValidator extends ConstraintValidator
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

        $counter = $this->em->getRepository('ApiBundle:Notebook')
            ->countNotebooks($user);

        if ((int)$counter[0]['amount'] > $constraint->getLimit()) {
            $this->context->buildViolation($constraint->getMessage(), ['{{ limit }}' => $constraint->getLimit()])
                ->addViolation();
        }
    }
}

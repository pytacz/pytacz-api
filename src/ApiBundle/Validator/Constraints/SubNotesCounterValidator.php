<?php

namespace ApiBundle\Validator\Constraints;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;

class SubNotesCounterValidator extends ConstraintValidator
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
    public function validate($id, Constraint $constraint)
    {
        $note = $this->em->getRepository('ApiBundle:Note')
            ->findOneBy(['id' => $id]);

        $counter = $this->em->getRepository('ApiBundle:Note')
            ->countSubNotes($note);

        if ((int)$counter[0]['amount'] >= $constraint->getLimit()) {
            $this->context->buildViolation($constraint->getMessage(), ['{{ limit }}' => $constraint->getLimit()])
                ->addViolation();
        }
    }
}

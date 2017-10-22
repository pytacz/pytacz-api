<?php

namespace ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class NotebookNameUnique extends Constraint
{
    private $message;

    public function __construct($options)
    {
        $this->message = $options['message'];
    }
    public function getMessage()
    {
        return $this->message;
    }
    public function getTargets()
    {
        return Constraint::PROPERTY_CONSTRAINT;
    }
}

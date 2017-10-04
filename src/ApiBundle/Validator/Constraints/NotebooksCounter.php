<?php

namespace ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class NotebooksCounter extends Constraint
{
    private $message;
    private $limit;

    public function __construct($options)
    {
        $this->message = $options['message'];
        $this->limit = $options['limit'];
    }
    public function getMessage()
    {
        return $this->message;
    }
    public function getLimit()
    {
        return $this->limit;
    }
    public function getTargets()
    {
        return Constraint::PROPERTY_CONSTRAINT;
    }
}

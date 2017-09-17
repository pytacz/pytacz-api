<?php

namespace ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class NoteContent extends Constraint
{
    private $message = [
        'depth' => '',
        'labelLengthMin' => '',
        'labelLengthMax' => '',
        'contentLengthMin' => '',
        'contentLengthMax' => '',
        'regex' => '',
        'invalidData' => ''
    ];
    private $labelLength = [
        'min' => '',
        'max' => ''
    ];
    private $contentLength = [
        'min' => '',
        'max' => ''
    ];
    private $depth;
    private $regex;

    public function __construct($options)
    {
        $this->depth = $options['depth'];
        $this->message['depth'] = $options['depthMessage'];

        $this->labelLength['min'] = $options['labelLengthMin'];
        $this->labelLength['max'] = $options['labelLengthMax'];
        $this->message['labelLengthMin'] = $options['labelLengthMinMessage'];
        $this->message['labelLengthMax'] = $options['labelLengthMaxMessage'];

        $this->contentLength['min'] = $options['contentLengthMin'];
        $this->contentLength['max'] = $options['contentLengthMax'];
        $this->message['contentLengthMin'] = $options['contentLengthMinMessage'];
        $this->message['contentLengthMax'] = $options['contentLengthMinMessage'];

        $this->regex = $options['regex'];
        $this->message['regex'] = $options['regexMessage'];

        $this->message['invalidData'] = $options['invalidDataMessage'];
    }
    public function getMessage()
    {
        return $this->message;
    }
    public function getLabelLength()
    {
        return $this->labelLength;
    }
    public function getContentLength()
    {
        return $this->contentLength;
    }
    public function getDepth()
    {
        return $this->depth;
    }
    public function getRegex()
    {
        return $this->regex;
    }
    public function getTargets()
    {
        return Constraint::PROPERTY_CONSTRAINT;
    }
}

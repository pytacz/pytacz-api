<?php

namespace ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NoteContentValidator extends ConstraintValidator
{
    private function arrayDepth(array $array)
    {
        $maxDepth = 1;

        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = $this->arrayDepth($value) + 1;

                if ($depth > $maxDepth) {
                    $maxDepth = $depth;
                }
            }
        }
        return $maxDepth;
    }

    private function contentValidation(string $value, Constraint $constraint)
    {
        $maxLength = $constraint->getContentLength()['max'];
        if (strlen($value)>$maxLength) {
            $this->context->buildViolation($constraint->getMessage()['contentLengthMax'], ['{{ limit }}' => $maxLength])
                ->addViolation();
        }
        $minLength = $constraint->getContentLength()['min'];
        if (strlen($value)<$minLength) {
            $this->context->buildViolation($constraint->getMessage()['contentLengthMin'], ['{{ limit }}' => $minLength])
                ->addViolation();
        }
    }

    private function labelValidation(string $value, Constraint $constraint)
    {
        $maxLength = $constraint->getLabelLength()['max'];
        if (strlen($value)>$maxLength) {
            $this->context->buildViolation($constraint->getMessage()['labelLengthMax'], ['{{ limit }}' => $maxLength])
                ->addViolation();
        }
        $minLength = $constraint->getLabelLength()['min'];
        if (strlen($value)<$minLength) {
            $this->context->buildViolation($constraint->getMessage()['labelLengthMin'], ['{{ limit }}' => $minLength])
                ->addViolation();
        }
        if (!preg_match($constraint->getRegex(), $value, $matches)) {
            $this->context->buildViolation($constraint->getMessage()['regex'])
                ->addViolation();
        }
    }

    public function validate($values, Constraint $constraint)
    {
        $values = json_decode($values, true);
        if (is_array($values)) {
            $parentLabel = key($values);

            if ($this->arrayDepth($values[$parentLabel]) > $constraint->getDepth()) {
                $this->context->buildViolation($constraint->getMessage()['depth'], ['{{ limit }}' => $constraint->getDepth()])
                    ->addViolation();
                return;
            }

            $this->labelValidation($parentLabel, $constraint);

            if (is_array($values[$parentLabel])) {
                foreach ($values[$parentLabel] as $key => $value) {
                    $this->labelValidation($key, $constraint);
                    $this->contentValidation($value, $constraint);
                }
            }
        } else {
            $this->context->buildViolation($constraint->getMessage()['invalidData'])
                ->addViolation();
        }
    }
}

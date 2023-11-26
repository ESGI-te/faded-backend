<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateTimeAfterNowValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        $now = new \DateTime('now');
        $now->setTime($now->format('H'), $now->format('i'));

        if ($value < $now) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}

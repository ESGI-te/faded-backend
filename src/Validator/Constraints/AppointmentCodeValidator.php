<?php

namespace App\Validator\Constraints;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AppointmentCodeValidator extends ConstraintValidator
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function validate($value, Constraint $constraint): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $codeFromQuery = $request->query->get('code');
        $entity = $this->context->getObject();
        $existingCode = $entity->getCode();

        if ($existingCode !== $codeFromQuery) {
            $this->context->buildViolation($constraint->invalidCodeMessage)->addViolation();
        }
    }
}

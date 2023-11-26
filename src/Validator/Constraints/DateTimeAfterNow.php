<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;


#[\Attribute]
class DateTimeAfterNow extends Constraint
{
    public string $message = 'The date and time must be later than or equal to the current date and time.';
}

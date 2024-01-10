<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AppointmentCode extends Constraint
{
    public $invalidCodeMessage = 'The code is invalid.';

}

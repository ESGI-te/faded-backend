<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidPassword extends Constraint
{
    public $invalidPassword = 'Invalid password';

}

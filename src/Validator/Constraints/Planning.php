<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Planning extends Constraint
{
    public $missingDaysMessage = 'One or more days are missing in the planning.';
    public $missingHoursMessage = '{{ type }} hours are missing for day "{{ day }}".';
    public $invalidDateTimeMessage = 'Invalid date or time format for day "{{ day }}".';
}

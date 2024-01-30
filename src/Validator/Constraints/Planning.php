<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class Planning extends Constraint
{
    public $missingDaysMessage = 'One or more days are missing in the planning.';
    public $missingHoursMessage = '{{ type }} hours are missing for day "{{ day }}".';
    public $missingIsOpenMessage = 'isOpen is missing for day {{ day }}';
    public $invalidIsOpenTypeMessage = '{{ type }} type is provided for isOpen on {{ day }}. Please set it to true or false.';
    public $invalidDateTimeMessage = 'Invalid date or time format for day "{{ day }}".';
}

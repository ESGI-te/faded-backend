<?php

namespace App\Validator\Constraints;

use App\Enum\DaysEnum;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class PlanningValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $days = array_keys($value);
        $daysEnumValues = array_column(DaysEnum::cases(), 'value');
        $missingDays = array_diff($daysEnumValues, $days);

        if (count($missingDays) > 0) {
            $this->context->buildViolation($constraint->missingDaysMessage)
                ->addViolation();
        }

        foreach ($value as $day => $hours) {

            if (empty($hours)) return;

            if (!isset($hours['open']) || !isset($hours['close'])) {

                $missingType = !isset($hours['open']) ? 'Open' : 'Close';

                $this->context->buildViolation($constraint->missingHoursMessage)
                    ->setParameters(['{{ day }}' => $day, '{{ type }}' => $missingType])
                    ->addViolation();
                return;
            }

            $openDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $hours['open']);
            $closeDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $hours['close']);

            if (!$openDateTime || !$closeDateTime) {
                $this->context->buildViolation($constraint->invalidDateTimeMessage)
                    ->setParameter('{{ day }}', $day)
                    ->addViolation();
            }
        }
    }
}

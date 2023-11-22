<?php

declare(strict_types=1);

namespace App\Enum;

enum AppointmentStatusEnum: string
{
    case FINISHED = 'finished';
    case PLANNED = 'planned';
    case CANCELED = 'canceled';

    public static function getValues(): array
    {
        $cases = self::cases();
        return array_map(static fn(\UnitEnum $case) => $case->value, $cases);
    }
}

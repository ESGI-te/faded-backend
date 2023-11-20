<?php

declare(strict_types=1);

namespace App\Enum;

enum AppointmentStatusEnum: string
{
    case FINISHED = StatusEnum::FINISHED->value;
    case PLANIFIED = StatusEnum::PLANIFIED->value;
    case CANCELED = StatusEnum::CANCELED->value;
}

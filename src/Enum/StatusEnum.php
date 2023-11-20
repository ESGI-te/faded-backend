<?php

declare(strict_types=1);

namespace App\Enum;

enum StatusEnum: string
{
    case PENDING = 'pending';
    case FINISHED = 'finished';
    case FAILED = 'failed';
    case PLANIFIED = 'planified';
    case CANCELED = 'canceled';
}

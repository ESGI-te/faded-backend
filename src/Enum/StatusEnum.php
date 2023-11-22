<?php

declare(strict_types=1);

namespace App\Enum;

enum StatusEnum: string
{
    case PENDING = 'pending';
    case FINISHED = 'finished';
    case FAILED = 'failed';
    case PLANNED = 'planned';
    case CANCELED = 'canceled';

    public static function getValues(): array
    {
        $cases = self::cases();
        return array_map(static fn(StatusEnum $case) => $case->value, $cases);
    }
}

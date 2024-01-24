<?php

declare(strict_types=1);

namespace App\Enum;

enum EstablishmentStatusEnum: string
{
    case ACTIVE = 'active';
    case DRAFT = 'draft';

    public static function getValues(): array
    {
        $cases = self::cases();
        return array_map(static fn(\UnitEnum $case) => $case->value, $cases);
    }
}

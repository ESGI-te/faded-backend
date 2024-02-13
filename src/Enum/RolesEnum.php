<?php

declare(strict_types=1);

namespace App\Enum;

enum RolesEnum: string
{
    case USER = 'ROLE_USER';
    case PROVIDER = 'ROLE_PROVIDER';
    case ADMIN = 'ROLE_ADMIN';

    case BARBER = 'ROLE_BARBER';

    public static function getValues(): array
    {
        $cases = self::cases();
        return array_map(static fn(\UnitEnum $case) => $case->value, $cases);
    }
}
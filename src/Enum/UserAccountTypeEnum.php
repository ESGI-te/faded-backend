<?php

declare(strict_types=1);

namespace App\Enum;

enum UserAccountTypeEnum: string
{
    case USER = 'user';
    case BARBER = 'barber';
    case MANAGER = 'manager';
    case ADMIN = 'admin';
}

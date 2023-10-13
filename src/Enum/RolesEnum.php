<?php

declare(strict_types=1);

namespace App\Enum;

enum RolesEnum: string
{
    case USER = 'ROLE_USER';
    case BARBER = 'ROLE_BARBER';
    case MANAGER = 'ROLE_MANAGER';
    case ADMIN = 'ROLE_ADMIN';
}
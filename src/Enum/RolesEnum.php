<?php

declare(strict_types=1);

namespace App\Enum;

enum RolesEnum: string
{
    case USER = 'ROLE_USER';
    case PROVIDER = 'ROLE_PROVIDER';
    case ADMIN = 'ROLE_ADMIN';
}
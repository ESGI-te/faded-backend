<?php

declare(strict_types=1);

namespace App\Enum;

enum EmailSenderEnum:string
{
    case TEO = 'Teo <teo@barbers.hair>';

    case CHAKIB = 'Chakib <chakib@barbers.hair>';

    case WELCOME = 'Welcome <welcome@barbers.hair>';

    case NO_REPLY = 'No Reply <noreply@barbers.hair>';

    case CONTACT = 'Contact <contact@barbers.hair>';


}

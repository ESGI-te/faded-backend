<?php

namespace App\Dto;

use App\Validator\Constraints\ValidPassword;
use Symfony\Component\Serializer\Annotation\Groups;

final class UserDto
{
    #[Groups(['user-update'])]
    #[ValidPassword(groups: ['user-update'])]
    public string $currentPassword;
}
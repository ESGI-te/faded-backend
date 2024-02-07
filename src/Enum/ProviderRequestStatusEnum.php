<?php
declare(strict_types=1);

namespace App\Enum;

enum ProviderRequestStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';


    public static function getValues(): array
    {
        $cases = self::cases();
        return array_map(static fn(\UnitEnum $case) => $case->value, $cases);
    }

}

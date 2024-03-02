<?php

declare(strict_types=1);

namespace App\Entity\Enum;

enum UserRole: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_ADMIN = 'ROLE_ADMIN';

    public static function getSignupAllowedValues(): array
    {
        return [
            self::ROLE_USER,
        ];
    }
}

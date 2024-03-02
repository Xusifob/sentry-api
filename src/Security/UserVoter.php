<?php

namespace App\Security;

use App\Entity\User;

class UserVoter extends IEntityVoter
{
    public function canUpdate(User $subject, User $user): bool
    {
        return $user->getId() === $subject->getId();
    }

    public function getSupportedAttributes(): array
    {
        return [
            self::UPDATE => 'canUpdate',
        ];
    }

    public function getSupportedClass(): string
    {
        return User::class;
    }
}

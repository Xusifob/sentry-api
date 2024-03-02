<?php

namespace App\Security\Voter;

use App\Entity\SentryException;
use App\Entity\User;
use App\Security\IEntityVoter;

class SentryExceptionVoter extends IEntityVoter
{
    public function getSupportedClass(): string
    {
        return SentryException::class;
    }

    public function canUpdate(SentryException $subject, User $user): bool
    {
        return $user->getId() === $subject->owner->getId();
    }

    public function canDelete(SentryException $subject, User $user): bool
    {
        return $user->getId() === $subject->owner->getId();
    }

    protected function getSupportedAttributes(): array
    {
        return [
            self::UPDATE => 'canUpdate',
            self::DELETE => 'canDelete',
        ];
    }
}

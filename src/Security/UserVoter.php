<?php

namespace App\Security;

use App\Entity\User;
use App\Security\Voter\BaseVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class UserVoter extends BaseVoter
{
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $user->getId() === $subject->getId();
    }

    function getSupportedAttributes(): array
    {
        return [self::UPDATE];
    }

    function getSupportedClass(): string|array
    {
        return User::class;
    }
}

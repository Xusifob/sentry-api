<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class IEntityVoter extends Voter
{
    final public const VIEW = 'VIEW';

    final public const CREATE = 'CREATE';

    final public const UPDATE = 'UPDATE';

    final public const DELETE = 'DELETE';

    public function __construct(protected EntityManagerInterface $em)
    {
    }

    abstract protected function getSupportedAttributes(): array;

    abstract protected function getSupportedClass(): string;

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User|null $user */
        $user = $token->getUser();

        $method = $this->getSupportedAttribute($attribute);

        if (!($user instanceof User)) {
            return false;
        }

        return $this->$method($subject, $user);
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$this->supportsAttribute($attribute)) {
            return false;
        }

        return $this->supportsEntity($subject);
    }

    public function supportsAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->getSupportedAttributes());
    }

    public function getSupportedAttribute(string $attribute): string
    {
        $attributes = $this->getSupportedAttributes();

        return $attributes[$attribute];
    }

    public function supportsEntity(mixed $subject): bool
    {
        $class = $this->getSupportedClass();

        return $subject instanceof $class;
    }
}

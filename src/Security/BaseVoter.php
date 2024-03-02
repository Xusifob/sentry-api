<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class BaseVoter extends Voter
{
    public const CREATE = 'CREATE';

    public const UPDATE = 'UPDATE';

    public const DELETE = 'DELETE';


    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, $this->getSupportedAttributes())) {
            return false;
        }

        $class = $this->getSupportedClass();

        if (is_string($class)) {
            return $subject instanceof $class;
        }

        if (is_array($class)) {
            foreach ($class as $c) {
                if ($subject instanceof $c) {
                    return true;
                }
            }
        }

        return false;
    }

    abstract function getSupportedAttributes(): array;


    abstract function getSupportedClass(): string|array;
}

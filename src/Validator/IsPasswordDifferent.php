<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class IsPasswordDifferent extends Constraint
{
    public function __construct(
        public string $differentPasswordMessage = 'Your current password is invalid',
        mixed $options = null,
        array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options, $groups, $payload);
    }

    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT];
    }
}

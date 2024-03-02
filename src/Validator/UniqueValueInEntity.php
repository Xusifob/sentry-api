<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueValueInEntity extends Constraint
{
    public function __construct(
        public readonly string $entityClass,
        public readonly string $field,
        public readonly string $message = 'This value is already used.',
        mixed $options = null,
        array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options, $groups, $payload);
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

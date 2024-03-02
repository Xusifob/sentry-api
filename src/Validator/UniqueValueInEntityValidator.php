<?php

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueValueInEntityValidator extends ConstraintValidator
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param UniqueValueInEntity $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        // @phpstan-ignore-next-line
        $entityRepository = $this->em->getRepository($constraint->entityClass);

        if (!is_scalar($constraint->field)) {
            throw new \InvalidArgumentException('"field" parameter should be any scalar type');
        }

        $field = $constraint->field;

        $searchResults = $entityRepository->findBy([
            $field => $value->$field,
        ]);

        if (count($searchResults) > 0) {
            $this->context->buildViolation($constraint->message)
                ->atPath($field)
                ->addViolation();
        }
    }
}

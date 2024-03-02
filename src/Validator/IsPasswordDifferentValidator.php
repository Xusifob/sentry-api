<?php

namespace App\Validator;

use App\Dto\RepeatPasswordInterface;
use App\Dto\User\Input\UpdatePasswordDto;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsPasswordDifferentValidator extends ConstraintValidator
{
    public function __construct(private readonly Security $security, private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * @param RepeatPasswordInterface $value
     * @param IsPasswordDifferent     $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!($value instanceof UpdatePasswordDto)) {
            throw new \InvalidArgumentException('The value must be an instance of UpdatePasswordDto');
        }

        $user = $this->security->getUser();

        if (!($user instanceof User)) {
            return;
        }

        if (!$this->passwordHasher->isPasswordValid($user, $value->currentPassword)) {
            $this->context->buildViolation($constraint->differentPasswordMessage)
                ->atPath('currentPassword')
                ->addViolation();
        }
    }
}

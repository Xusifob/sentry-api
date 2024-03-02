<?php

namespace App\Dto\User\Input;

use App\Dto\RepeatPasswordInterface;
use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Validator\IsPasswordValid;
use App\Validator\UniqueValueInEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[IsPasswordValid(
    notMatchPasswordMessage: 'user.password.not_match',
    weakPasswordMessage: 'user.password.weak'
)]
#[UniqueValueInEntity(
    entityClass: User::class,
    field: 'email',
    message: 'user.email.already_used',
)]
class SignupInputDto implements RepeatPasswordInterface
{
    use RepeatPasswordTrait;

    #[Groups('write')]
    #[Assert\NotBlank(message: 'user.email.invalid')]
    #[Assert\Email(message: 'user.email.invalid')]
    public ?string $email = null;

    #[Groups('write')]
    #[Assert\NotBlank(message: 'user.given_name.invalid')]
    public ?string $givenName = null;

    #[Groups('write')]
    #[Assert\NotBlank(message: 'user.family_name.invalid')]
    public ?string $familyName = null;

    #[Groups('write')]
    #[Assert\NotBlank(message: 'user.role.invalid')]
    public ?UserRole $role = UserRole::ROLE_USER;
}

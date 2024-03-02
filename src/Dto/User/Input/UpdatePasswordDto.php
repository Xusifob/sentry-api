<?php

namespace App\Dto\User\Input;

use App\Dto\RepeatPasswordInterface;
use App\Validator\IsPasswordDifferent;
use App\Validator\IsPasswordValid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[IsPasswordValid(
    notMatchPasswordMessage: 'user.password.not_match',
    weakPasswordMessage: 'user.password.weak'
)]
#[IsPasswordDifferent(
    differentPasswordMessage: 'user.password.same'
)]
class UpdatePasswordDto implements RepeatPasswordInterface
{
    use RepeatPasswordTrait;

    #[Groups('write')]
    #[Assert\NotBlank(message: 'user.current_password.invalid')]
    public ?string $currentPassword = null;
}

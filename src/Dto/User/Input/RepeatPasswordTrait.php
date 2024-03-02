<?php

namespace App\Dto\User\Input;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait RepeatPasswordTrait
{
    #[Groups('write')]
    #[Assert\NotBlank(message: 'user.password.invalid')]
    #[Assert\NotCompromisedPassword(message: 'user.password.invalid')]
    public ?string $password = null;

    #[Groups('write')]
    #[Assert\NotBlank(message: 'user.repeat_password.invalid')]
    public ?string $repeatPassword = null;

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function isPasswordRepeated(): bool
    {
        return $this->password && ($this->password === $this->repeatPassword);
    }
}

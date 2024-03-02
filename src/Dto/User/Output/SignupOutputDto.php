<?php

namespace App\Dto\User\Output;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\Enum\UserRole;
use App\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;

class SignupOutputDto
{
    #[Groups('read')]
    public ?string $email = null;

    #[Groups('read')]
    public ?string $givenName = null;

    #[Groups('read')]
    public ?string $familyName = null;

    #[Groups('read')]
    public ?UserRole $role = null;

    #[Groups('read')]
    public bool $activationEmailSent = false;

    #[ApiProperty(readable: false, writable: false)]
    public ?User $entity = null;
}

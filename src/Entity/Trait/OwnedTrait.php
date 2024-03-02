<?php

declare(strict_types=1);

namespace App\Entity\Trait;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

trait OwnedTrait
{
    #[ApiProperty(writable: false)]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    public ?User $owner = null;

    public function isOwnedBy(?User $user): bool
    {
        if (null === $user) {
            return false;
        }

        return $this->owner === $user;
    }

    public static function isPrivate(): bool
    {
        return true;
    }
}

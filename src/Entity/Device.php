<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Entity\Trait\OwnedTrait;
use App\State\OneSignal\PostDeviceProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity()]
#[ORM\Table(name: 'one_signal_device')]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: 'one_signal/devices',
            processor: PostDeviceProcessor::class,
        ),
    ]
)]
class Device extends Entity implements IOwnedEntity
{
    use OwnedTrait;

    #[Assert\NotBlank(message: 'device.token.not_blank')]
    #[ORM\Column(name: 'token', type: 'string', length: 255, unique: true, nullable: false)]
    #[Groups(['write', 'read'])]
    public ?string $token;

    public function isExpired(): bool
    {
        $date = $this->updatedDate ?? $this->createdDate;

        return $date->getTimestamp() < (time() - 60 * 60 * 24 * 30);
    }
}

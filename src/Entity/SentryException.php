<?php

namespace App\Entity;


use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Entity\Enum\ExceptionLevel;
use App\Entity\Trait\EntityTrait;
use App\Entity\Trait\OwnedTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(
    operations: [
        new GetCollection(),
        new Patch(
            security: "is_granted('UPDATE',object)",
        ),
        new Delete(
            security: "is_granted('DELETE',object)",
        ),
        new Get(
            security: "is_granted('VIEW',object)",
        )
    ],
)]
#[ORM\Entity()]
class SentryException implements IEntity, IOwnedEntity
{

    use EntityTrait;
    use OwnedTrait;

    #[Groups(['sentry_exception:read'])]
    #[ApiProperty(readable: true)]
    #[ORM\Column(type: "string", length: 255, nullable: false)]
    public ?string $title = null;

    #[Groups(['sentry_exception:read'])]
    #[ApiProperty(readable: true)]
    #[ORM\Column(type: "string", length: 255, unique: true, nullable: false)]
    public ?string $eventId = null;

    #[Groups(['sentry_exception:read'])]
    #[ApiProperty(readable: true)]
    public ?int $projectId = null;

    #[Groups(['sentry_exception:read'])]
    #[ApiProperty(readable: true)]
    #[ORM\Column(type: "string", length: 255, nullable: false)]
    public ?string $message = null;

    #[Groups(['sentry_exception:read'])]
    #[ApiProperty(readable: true)]
    #[ORM\Column(name: 'release_name', type: "string", length: 255, nullable: true)]
    public ?string $release = null;

    #[Groups(['sentry_exception:read'])]
    #[ApiProperty(readable: true)]
    #[ORM\Column(type: "string", length: 255, nullable: false)]
    public ?string $platform = null;

    #[Groups(['sentry_exception:read'])]
    #[ApiProperty(readable: true)]
    #[ORM\Column(type: "json", length: 255, nullable: false)]
    public array $exception = [];

    #[Groups(['sentry_exception:read'])]
    #[ApiProperty(readable: true)]
    #[ORM\Column(type: "json", length: 255, nullable: false)]
    public array $tags = [];

    #[Groups(['sentry_exception:read'])]
    #[ApiProperty(readable: true)]
    #[ORM\Column(type: "text", enumType: ExceptionLevel::class)]
    public ExceptionLevel $level;

    #[Groups(['sentry_exception:read'])]
    #[ApiProperty(readable: true)]
    #[ORM\Column(type: "string", length: 255, nullable: false)]
    public string $location;

    #[Groups(['sentry_exception:read'])]
    #[ApiProperty(readable: true)]
    #[ORM\Column(type: "string", length: 255, nullable: false)]
    public string $url;

    #[Groups(['sentry_exception:read'])]
    #[ApiProperty(readable: true)]
    #[ORM\Column(type: "json", length: 255, nullable: false)]
    public array $request = [];


    #[ApiFilter(filterClass: BooleanFilter::class)]
    #[Groups(['sentry_exception:read', 'sentry_exception:write'])]
    #[ApiProperty(readable: true)]
    #[ORM\Column(type: "boolean",)]
    public bool $archived = false;

}

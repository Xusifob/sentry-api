<?php

declare(strict_types=1);

namespace App\Entity\Trait;

use ApiPlatform\Metadata\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\UuidV6;

use function Symfony\Component\String\u;

trait EntityTrait
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true, nullable: false)]
    #[ApiProperty(schema: [
        'type' => 'string',
        'format' => 'uuid',
        'nullable' => false,
    ], iris: ['https://schema.org/identifier'])]
    #[Groups(['read', 'write'])]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected null|UuidV6|string $id = null;

    #[Groups(['read'])]
    #[ApiProperty(readable: true, writable: false)]
    #[ORM\Column(name: 'created_date', type: 'datetime_immutable', nullable: false)]
    protected ?\DateTimeInterface $createdDate = null;

    #[ORM\Column(name: 'updated_date', type: 'datetime', nullable: true)]
    protected ?\DateTimeInterface $updatedDate = null;

    public function getId(): null|UuidV6|string
    {
        return $this->id;
    }

    public function setId(null|UuidV6|string $id): void
    {
        $this->id = $id;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(?\DateTimeInterface $createdDate): void
    {
        $this->createdDate = $createdDate;
    }

    public function getUpdatedDate(): ?\DateTimeInterface
    {
        return $this->updatedDate;
    }

    public function setUpdatedDate(?\DateTimeInterface $updatedDate): void
    {
        $this->updatedDate = $updatedDate;
    }

    #[ApiProperty(readable: false, writable: false)]
    public function setEntityData(array $data = []): void
    {
        foreach ($data as $key => $val) {
            $this->setEntityValue($key, $val);
        }
    }

    #[ApiProperty(readable: false, writable: false)]
    public function setEntityValue(string $key, string|int|array|null|object $value): void
    {
        $vars = get_object_vars($this);
        $camelizeKey = u($key)->camel()->toString();

        // Setter found -> $this->setMyProperty($value);
        $setter = "set$camelizeKey";
        if (method_exists($this, $setter)) {
            $this->$setter($value);

            return;
        }

        // Adder found -> $this->addMyProperty($value);
        $setter = "add$camelizeKey";
        if (method_exists($this, $setter)) {
            $this->$setter($value);

            return;
        }

        // Property found -> $this->myProperty = $value;
        if (array_key_exists($key, $vars)) {
            $this->$key = $value;
        }
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}

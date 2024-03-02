<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Uid\UuidV6;

/**
 * @property string|UuidV6|null $id
 */
interface IEntity extends \Stringable
{
    public function getId(): null|UuidV6|string;

    public function getCreatedDate(): ?\DateTimeInterface;

    public function setCreatedDate(?\DateTimeInterface $createdDate): void;

    public function getUpdatedDate(): ?\DateTimeInterface;

    public function setUpdatedDate(?\DateTimeInterface $updatedDate): void;
}

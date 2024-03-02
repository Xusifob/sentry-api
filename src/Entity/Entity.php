<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\Trait\EntityTrait;
use App\Entity\Trait\RightfulEntityTrait;

/**
 * This is the base doctrine Entity.
 */
abstract class Entity implements IRightfulEntity
{
    use EntityTrait;
    use RightfulEntityTrait;

    public function __construct(array $data = [])
    {
        $this->setEntityData($data);
    }

    #[ApiProperty(readable: false, writable: false)]
    public static function parseId(string $id): string
    {
        $id = explode('/', $id);

        return $id[count($id) - 1];
    }
}

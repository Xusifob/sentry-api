<?php

declare(strict_types=1);

namespace App\Doctrine\EntityListener;

use App\Entity\IEntity;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class EntityListener
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!($object instanceof IEntity)) {
            return;
        }

        if ($object->getCreatedDate()) {
            return;
        }

        $object->setCreatedDate(new \DateTimeImmutable());
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!($object instanceof IEntity)) {
            return;
        }

        $object->setUpdatedDate(new \DateTime());
    }
}

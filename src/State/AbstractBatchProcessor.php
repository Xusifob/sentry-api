<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiPlatform\Paginator;
use App\Dto\BatchProcessorEntryInterface;
use App\Dto\BatchProcessorInterface;
use App\Entity\Entity;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractBatchProcessor implements ProcessorInterface
{
    public function __construct(protected readonly ProcessorInterface $decorated, protected readonly EntityManagerInterface $em)
    {
    }

    /**
     * @param BatchProcessorInterface $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Paginator
    {
        $entities = [];

        foreach ($data->getEntities() as $entity) {
            $entity = $this->dtoToEntity($entity);
            $entities[] = $this->decorated->process($entity, $operation, $uriVariables, $context);
        }

        return new Paginator($entities, count($entities), count($entities), 1);
    }

    protected function fetchEntity(BatchProcessorEntryInterface $dto, string $entityClass): Entity
    {
        $entity = null;
        if ($dto->getId()) {
            // @phpstan-ignore-next-line
            $entity = $this->em->getRepository($entityClass)->find(
                Entity::parseId($dto->getId())
            );
        }

        if (!$entity) {
            $entity = new $entityClass();
        }

        return $entity;
    }

    abstract public function dtoToEntity(BatchProcessorEntryInterface $dto): Entity;
}

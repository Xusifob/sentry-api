<?php

namespace App\EventListener;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SubresourceListener implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['preRead', EventPriorities::PRE_READ],
            ],
        ];
    }

    public function preRead(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $operation = $request->get('_api_operation');

        if (!($operation instanceof HttpOperation)) {
            return;
        }

        $variables = $operation->getUriVariables();

        if (null === $variables) {
            return;
        }

        $parents = new \stdClass();

        foreach ($operation->getUriVariables() as $uriVariable) {
            if (!($uriVariable instanceof Link)) {
                continue;
            }

            $id = $request->get('_route_params')[$uriVariable->getParameterName()] ?? null;

            if (null === $id) {
                continue;
            }

            $identifier = $uriVariable->getIdentifiers()[0] ?? null;

            if (null === $identifier) {
                continue;
            }

            // @phpstan-ignore-next-line
            $parents->{$uriVariable->getParameterName()} = $this->em->getRepository($uriVariable->getFromClass())->findOneBy([
                $identifier => $id,
            ]);
        }

        $request->attributes->set('parents', $parents);
    }
}

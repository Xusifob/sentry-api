<?php

namespace App\State\OneSignal;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Device;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class PostDeviceProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
    ) {
    }

    /**
     * @param Device $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Response
    {
        $existing = $this->em->getRepository(Device::class)->findOneBy(['token' => $data->token]);

        if ($existing) {
            $existing->setUpdatedDate(new \DateTimeImmutable());
            $data = $existing;
        }

        /** @var User $user */
        $user = $this->security->getUser();

        $data->owner = $user;

        $this->em->persist($data);
        $this->em->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\User\Input\UpdatePasswordDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

final class UpdatePasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security
    ) {
    }

    /**
     * @param UpdatePasswordDto $data
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): Response
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $user->plainPassword = $data->getPassword();

        $this->em->getUnitOfWork()->scheduleForUpdate($user);
        $this->em->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}

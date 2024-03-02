<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @return User[]
     */
    public function findByRole(UserRole $role): array
    {
        return $this->createQueryBuilder('u')
            ->where('JSON_GET_TEXT(u.roles,0) = :role')
            ->setParameter('role', $role)
            ->getQuery()
            ->getResult();
    }

    public function findOneByProjectId(int $projectId): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.projects LIKE :projectId')
            ->setParameter('projectId', "%$projectId%")
            ->getQuery()
            ->getOneOrNullResult();
    }
}

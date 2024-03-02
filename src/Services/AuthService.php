<?php

namespace App\Services;

use ApiPlatform\Metadata\Post;
use App\Dto\User\Input\SignupInputDto;
use App\Entity\User;
use App\State\User\SignupProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class AuthService
{
    public function __construct(
        private readonly int $tokenTtl,
        private readonly string $tokenParameterName,
        private readonly string $expirationParameterName,
        private readonly EntityManagerInterface $em,
        private readonly SignupProcessor $signupProcessor,
        private readonly JWTTokenManagerInterface $JWTTokenManager,
        private readonly RefreshTokenGeneratorInterface $refreshTokenGenerator,
    ) {
    }

    public function createUser(string $email, string $firstName, string $lastName): User
    {
        $existingUser = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($existingUser) {
            return $existingUser;
        }

        $signupDto = new SignupInputDto();
        $signupDto->email = $email;
        $signupDto->familyName = $lastName;
        $signupDto->givenName = $firstName;
        $signupDto->password = password_hash(
            $email.uniqid('password', true),
            PASSWORD_BCRYPT
        );
        $signupDto->repeatPassword = $signupDto->password;

        $output = $this->signupProcessor->process($signupDto, new Post());

        return $output->entity;
    }

    public function generateLoginResponse(User $user): array
    {
        $refreshToken = $this->getRefreshToken($user);

        return [
            'token' => $this->getJwt($user),
            $this->tokenParameterName => $refreshToken->getRefreshToken(),
            $this->expirationParameterName => $refreshToken->getValid()->getTimestamp(),
        ];
    }

    private function getJwt(User $user): string
    {
        return $this->JWTTokenManager->create($user);
    }

    private function getRefreshToken(User $user): RefreshTokenInterface
    {
        return $this->refreshTokenGenerator->createForUserWithTtl($user, $this->tokenTtl);
    }
}

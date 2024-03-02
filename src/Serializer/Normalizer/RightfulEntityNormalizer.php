<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Entity\IRightfulEntity;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class RightfulEntityNormalizer implements NormalizerInterface, NormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'APP_RIGHTFUL_ENTITY_NORMALIZER_ALREADY_CALLED';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
        private readonly Security $security,
        private readonly CacheInterface $cache
    ) {
    }

    /**
     * @param IRightfulEntity $object
     *
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $context[self::buildCalledKey($object)] = true;

        $token = $this->security->getToken();
        /** @var User|null $user */
        $user = $token?->getUser();

        $entity = str_replace('\\', '', get_class($object));

        foreach ($object->getRightKeys() as $right) {
            $cacheKey = "cache_{$user?->getId()}_{$right}_$entity";
            $vote = $this->cache->get(
                $cacheKey,
                function (ItemInterface $item) use ($right, $object, $token) {
                    $value = $token instanceof TokenInterface && $this->accessDecisionManager->decide($token, [$right], $object);
                    $item->set($value);
                    $item->expiresAt((new \DateTime())->modify('+ 1 day'));

                    return $value;
                }
            );

            $object->setRight(strtolower((string) $right), $vote);
        }

        return $this->normalizer->normalize($object, $format, $context);
    }

    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        if (!$data instanceof IRightfulEntity) {
            return false;
        }
        // Make sure we're not called twice
        return !isset($context[self::buildCalledKey($data)]);
    }

    private function buildCalledKey(IRightfulEntity $entity): string
    {
        return sprintf('%s_%s', self::ALREADY_CALLED, $entity->getId());
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return false;
    }
}

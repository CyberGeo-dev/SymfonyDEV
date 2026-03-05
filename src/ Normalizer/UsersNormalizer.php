<?php

namespace App\Normalizer;

use App\Entity\Users;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class UsersNormalizer implements ContextAwareNormalizerInterface
{

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Users;
    }

    public function normalize($user, string $format = null, array $context = []): array
    {
        return [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'role' => $user->getRole()->getId(),
            'mail' => $user->getMail(),
        ];
    }
}
<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class CustomNormalizer implements ContextAwareNormalizerInterface
{
    private $decorated;

    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $this->decorated->supportsNormalization($data, $format, $context);
    }

    public function normalize($data, string $format = null, array $context = []): array
    {
        $context['groups'][] = 'stays_list';
        $context['circular_reference_handler'] = function ($object) {
            return $object->getId();
        };
        return $this->decorated->normalize($data, $format, $context);
    }
}

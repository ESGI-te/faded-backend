<?php

namespace App\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Auth\User;
use App\Entity\Barber;
use App\Entity\Establishment;
use App\Entity\Provider;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class BarberPlainIdentifierDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private IriConverterInterface $iriConverter;

    public function __construct(IriConverterInterface $iriConverter)
    {
        $this->iriConverter = $iriConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if(!empty($data['provider'])) {
            $data['provider'] = $this->iriConverter->getIriFromResource(resource: Provider::class, context: ['uri_variables' => ['id' => $data['provider']]]);
        }
        if(!empty($data['user'])) {
            $data['user'] = $this->iriConverter->getIriFromResource(resource: User::class, context: ['uri_variables' => ['id' => $data['user']]]);
        }
        if(!empty($data['establishment'])) {
            $data['establishment'] = $this->iriConverter->getIriFromResource(resource: Establishment::class, context: ['uri_variables' => ['id' => $data['establishment']]]);
        }
        
        return $this->denormalizer->denormalize($data, $class, $format, $context + [__CLASS__ => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        $hasRelationship =
            !empty($data['provider'])
            || !empty($data['establishment'])
            || !empty($data['user']);

        return \in_array($format, ['json', 'jsonld'], true)
            && is_a($type, Barber::class, true)
            && $hasRelationship
            && !isset($context[__CLASS__]);
    }
}
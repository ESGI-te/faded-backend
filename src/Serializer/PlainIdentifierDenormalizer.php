<?php

namespace App\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Appointment;
use App\Entity\Auth\User;
use App\Entity\Barber;
use App\Entity\Establishment;
use App\Entity\Service;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class PlainIdentifierDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private $iriConverter;

    public function __construct(IriConverterInterface $iriConverter)
    {
        $this->iriConverter = $iriConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if($data['barber']) {
            $data['barber'] = $this->iriConverter->getIriFromResource(resource: Barber::class, context: ['uri_variables' => ['id' => $data['barber']]]);
        }
        if($data['establishment']) {
            $data['establishment'] = $this->iriConverter->getIriFromResource(resource: Establishment::class, context: ['uri_variables' => ['id' => $data['establishment']]]);
        }
        if($data['service']) {
            $data['service'] = $this->iriConverter->getIriFromResource(resource: Service::class, context: ['uri_variables' => ['id' => $data['service']]]);
        }
        if($data['user']) {
            $data['user'] = $this->iriConverter->getIriFromResource(resource: User::class, context: ['uri_variables' => ['id' => $data['user']]]);
        }

        return $this->denormalizer->denormalize($data, $class, $format, $context + [__CLASS__ => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        $hasRelationship =
            !empty($data['establishment'])
            || !empty($data['service'])
            || !empty($data['user']);

        return \in_array($format, ['json', 'jsonld'], true)
            && is_a($type, Appointment::class, true)
            && $hasRelationship
            && !isset($context[__CLASS__]);
    }
}
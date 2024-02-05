<?php

namespace App\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Establishment;
use App\Entity\Service;
use App\Entity\ServiceCategory;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class ServicePlainIdentifierDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
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
        if(!empty($data['category'])) {
            $data['category'] = $this->iriConverter->getIriFromResource(resource: ServiceCategory::class, context: ['uri_variables' => ['id' => $data['category']]]);
        }

        if (!empty($data['establishment']) && is_array($data['establishment'])) {
            foreach ($data['establishment'] as $key => $establishmentId) {
                // Replace each id in the array with its corresponding IRI
                $data['establishment'][$key] = $this->iriConverter->getIriFromResource(resource: Establishment::class, context: ['uri_variables' => ['id' => $establishmentId]]);
            }
        }

        return $this->denormalizer->denormalize($data, $class, $format, $context + [__CLASS__ => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        $hasRelationship = !empty($data['category']) || !empty($data['establishment']);

        return \in_array($format, ['json', 'jsonld'], true)
            && is_a($type, Service::class, true)
            && $hasRelationship
            && !isset($context[__CLASS__]);
    }
}
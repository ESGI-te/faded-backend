<?php


namespace App\Serializer;

use App\Entity\Provider;
use App\Service\ImageUploaderService;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ProviderImageDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private ObjectNormalizer $normalizer;
    private ImageUploaderService $imageUploaderService;

    public function __construct(ObjectNormalizer $normalizer, ImageUploaderService $imageUploaderService)
    {
        $this->normalizer = $normalizer;
        $this->imageUploaderService = $imageUploaderService;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        if (isset($data['image'])) {
            $imageUrl = $this->imageUploaderService->uploadImage($data['image'], [
                'folder' => 'provider'
            ]);

            $data['image'] = $imageUrl;
        }

        return $this->normalizer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return Provider::class === $type;
    }
}
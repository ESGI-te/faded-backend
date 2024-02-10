<?php


namespace App\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Establishment;
use App\Entity\Image;
use App\Service\ImageUploaderService;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class GalleryImageDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private ObjectNormalizer $normalizer;
    private ImageUploaderService $imageUploaderService;
    private IriConverterInterface $iriConverter;

    public function __construct(IriConverterInterface $iriConverter,ObjectNormalizer $normalizer, ImageUploaderService $imageUploaderService)
    {
        $this->normalizer = $normalizer;
        $this->imageUploaderService = $imageUploaderService;
        $this->iriConverter = $iriConverter;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        if (isset($data['url'])) {
            $imageUrl = $this->imageUploaderService->uploadImage($data['url'], [
                'folder' => 'establishment'
            ]);

            $data['url'] = $imageUrl;
        }

        if($data['establishment']) {
            $data['establishment'] = $this->iriConverter->getIriFromResource(resource: Establishment::class, context: ['uri_variables' => ['id' => $data['establishment']]]);
        }

        return $this->normalizer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = []): bool
    {
        return Image::class === $type;
    }
}
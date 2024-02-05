<?php

namespace App\Service;

use App\Entity\Image;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Doctrine\ORM\EntityManagerInterface;

class ImageUploaderService
{

    public function __construct(string $cloudinaryUrl, EntityManagerInterface $entityManager)
    {
        Configuration::instance($cloudinaryUrl);
    }

    public function uploadImage($file, $options): string
    {
        $upload = new UploadApi();
        $file = $upload->upload($file, [
            ...$options,
            'resource_type' => 'image'
        ]);
        return $file['secure_url'];
    }
}

<?php

namespace App\Service;

use App\Entity\Image;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Doctrine\ORM\EntityManagerInterface;

class ImageUploaderService
{
    private EntityManagerInterface $entityManager;

    public function __construct(string $cloudinaryUrl, EntityManagerInterface $entityManager)
    {
        Configuration::instance($cloudinaryUrl);
        $this->entityManager = $entityManager;
    }

    public function uploadImage($file, $options): Image
    {
        $upload = new UploadApi();
        $image = new Image();
        $file = $upload->upload($file, [
            ...$options,
            'resource_type' => 'image'
        ]);
        $image->setUrl($file['secure_url']);
        $this->entityManager->persist($image);
        $this->entityManager->flush();

        return $image;
    }
}

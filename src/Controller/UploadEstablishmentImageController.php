<?php

namespace App\Controller;

use App\Entity\Establishment;
use App\Service\ImageUploaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class UploadEstablishmentImageController extends AbstractController
{

    public function __invoke(Establishment $establishment, Request $request, ImageUploaderService $imageUploaderService): ?Establishment
    {
        $content = json_decode($request->getContent(), true);
        $uploadedFile = $content['file'] ?? null;

        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        try {
            $image = $imageUploaderService->uploadImage($uploadedFile, [
                'folder' => 'establishment'
            ]);
            $establishment->addImage($image);
            return $establishment;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }
}

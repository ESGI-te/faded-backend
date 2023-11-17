<?php

namespace App\Controller;

use App\Entity\Barber;
use App\Service\ImageUploaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class UploadBarberImageController extends AbstractController
{

    public function __invoke(Barber $barber, Request $request, ImageUploaderService $imageUploaderService): ?Barber
    {
        $content = json_decode($request->getContent(), true);
        $uploadedFile = $content['file'] ?? null;

        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        try {
            $image = $imageUploaderService->uploadImage($uploadedFile, [
                'folder' => 'barber'
            ]);
            $barber->setImage($image);
            return $barber;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }
}

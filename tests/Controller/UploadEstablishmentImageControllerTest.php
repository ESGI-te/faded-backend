<?php

namespace App\Tests\Controller;

use App\Controller\UploadEstablishmentImageController;
use App\Entity\Establishment;
use App\Entity\Image;
use App\Service\ImageUploaderService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UploadEstablishmentImageControllerTest extends WebTestCase
{
    public function testInvokeWithFile()
    {
        $establishment = new Establishment(); 
        $request = new Request([], [], [], [], [], [], json_encode(['file' => 'image.jpg']));

        $imageUploaderService = $this->createMock(ImageUploaderService::class);

        $mockImage = new Image();
        $mockImage->setUrl('path/to/image.jpg');

        $imageUploaderService->expects($this->once())
                             ->method('uploadImage')
                             ->with('image.jpg', ['folder' => 'establishment'])
                             ->willReturn($mockImage);

        $controller = new UploadEstablishmentImageController();
        $result = $controller($establishment, $request, $imageUploaderService);

        $this->assertInstanceOf(Establishment::class, $result);
    }

    public function testInvokeWithoutFile()
    {
        $this->expectException(BadRequestHttpException::class);

        $establishment = new Establishment();
        $request = new Request([], [], [], [], [], [], json_encode([]));

        $imageUploaderService = $this->createMock(ImageUploaderService::class);

        $controller = new UploadEstablishmentImageController();
        $controller($establishment, $request, $imageUploaderService);
    }
}

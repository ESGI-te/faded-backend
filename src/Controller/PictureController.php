<?php

namespace App\Controller;
use App\Entity\Media;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;


final class PictureController
{
    #[Route(path: 'image/{id}', name: 'image', methods: ['GET'])]
    public function image(string $id,MediaRepository $mediaRepository) : Response
    {

        $img = $mediaRepository->findOneById($id);

        $url = $img->get;

        $filepath = $this->storage->resolveUri($img);

        $response = new Response(file_get_contents($filepath));

        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $img);

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'image/png');

        return $response;
    }


}
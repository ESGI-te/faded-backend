<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class RedirectToApiController
{
    #[Route('/', name: 'redirect_to_api')]
    public function redirectToApi(): Response
    {
        return new RedirectResponse('/api');
    }
}
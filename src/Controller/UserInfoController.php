<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsController]
class UserInfoController extends AbstractController
{
    public function __invoke(): UserInterface
    {
        $user = $this->getUser();

        return $user;
    }
}

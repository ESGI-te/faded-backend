<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

class NewUserByDateRangeProvider implements ProviderInterface
{
    private UserRepository $userRepository;

    private Security $security;

    public function __construct( Security $security, UserRepository $userRepository )
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    /**
     * @throws \Exception
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        if(!$isAdmin) {
          throw new \Exception('You do not have permission for this query');
        }

        $startString = $context['filters']['start'];
        $endString = $context['filters']['end'];

        return $this->userRepository->findNewUsersByDateRange(new \DateTime($startString), new \DateTime($endString));
    }
}
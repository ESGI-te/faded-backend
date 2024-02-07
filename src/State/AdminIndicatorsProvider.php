<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\AppointmentRepository;
use App\Repository\ProviderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

class AdminIndicatorsProvider implements ProviderInterface
{

    private AppointmentRepository $appointmentRepository;
    private UserRepository $userRepository;

    private ProviderRepository $providerRepository;
    private Security $security;

    public function __construct(AppointmentRepository $appointmentRepository, Security $security, UserRepository $userRepository, ProviderRepository $providerRepository)
    {
        $this->userRepository = $userRepository;
        $this->providerRepository = $providerRepository;
        $this->appointmentRepository = $appointmentRepository;
        $this->security = $security;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

       // if(!$isAdmin) {
         //   throw new \Exception('You do not have permission for this query');
        //}

        $findNewUsers = $this->userRepository->findNewUsers();
        $filterNewUsers = array_filter($findNewUsers, function($user) {
            return $user->getRoles() === ['ROLE_USER'];
        });
        $newUsersCount = count($filterNewUsers);
        $totalProviders = $this->providerRepository->findTotalProviders();
        $cashFlow = $this->appointmentRepository->findAdminDailyIndicators();
        $dailyTurnover = $cashFlow * 0.15;

        return [
            'newUsers' => $newUsersCount,
            'totalProviders' => $totalProviders,
            'dailyTurnover' => $dailyTurnover
        ];
    }

}
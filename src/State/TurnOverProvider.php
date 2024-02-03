<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Provider;
use App\Repository\AppointmentRepository;
use Symfony\Bundle\SecurityBundle\Security;

class TurnOverProvider implements ProviderInterface
{

    private AppointmentRepository $appointmentRepository;

    private Security $security;

    public function __construct(AppointmentRepository $appointmentRepository, Security $security)
    {
        $this->appointmentRepository = $appointmentRepository;
        $this->security = $security;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        $provider = $user->getProvider();
        assert($provider instanceof Provider);
        $id = $provider->getId();

        $isProvider = $this->security->isGranted('ROLE_PROVIDER');
        $isBarber = $this->security->isGranted('ROLE_BARBER');

        if(!$isProvider && !$isBarber) {
            throw new \Exception('You do not have permission for this query');
        }

        $establishmentId = $context['filters']['establishmentId'] ?? null;

        if($isProvider && !$establishmentId && !$isBarber) {
            return $this->appointmentRepository->findGlobalTurnOver($id);
        }
        elseif ($establishmentId && $isProvider && !$isBarber){
            return $this->appointmentRepository->findGlobalTurnOver($id,$establishmentId);
        }
        elseif($isBarber) {
            return $this->appointmentRepository->findGlobalTurnOver($id,$establishmentId);
        }
        else {
            throw new \Exception("something went wrong");
        }
    }
}

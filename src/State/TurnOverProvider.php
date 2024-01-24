<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\AppointmentRepository;

class TurnOverProvider implements ProviderInterface
{

    private AppointmentRepository $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $providerId = $context['filters']['providerId'];
        $establishmentId = $context['filters']['establishmentId'];

        if($providerId && $establishmentId->isEmpty() ) {
            return $this->appointmentRepository->findGlobalTurnOver($providerId);
        }
        elseif ($establishmentId && !$providerId ){
            return $this->appointmentRepository->findGlobalTurnOver($providerId,$establishmentId);
        }
        else {
            throw new \Exception("something went wrong");
        }
    }
}

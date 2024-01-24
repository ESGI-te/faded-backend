<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\AppointmentRepository;

class TopServicesProvider implements ProviderInterface
{
    private AppointmentRepository $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $limit = $context['filters']['limit'];


        if ($limit) {
            return $this->appointmentRepository->findTopServicesByAppointmentCount($limit);
        }
        else {
            throw new \Exception("something went wrong");
        }
    }
}

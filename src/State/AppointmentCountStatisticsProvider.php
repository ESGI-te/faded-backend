<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\AppointmentRepository;

class AppointmentCountStatisticsProvider implements ProviderInterface
{

    private AppointmentRepository $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {

        $days = (int) $context['filters']['days'];

        if($days) {
            $appointments = $this->appointmentRepository->findByRangAppointments($days);
            return [
                'appointments' => $appointments
            ];
        }

        return null;
    }
}

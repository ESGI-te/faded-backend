<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\AppointmentRepository;

class AppointmentRateStatisticsProvider implements ProviderInterface
{
    private AppointmentRepository $appointmentRepository;

    public function __construct(AppointmentRepository $appointmentRepository)
    {
        $this->appointmentRepository = $appointmentRepository;
    }


    /**
     * @throws \Exception
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $startString = $context['filters']['start'];
        $endString = $context['filters']['end'];


        if ($startString && $endString) {
                $start = new \DateTime($startString);
                $end = new \DateTime($endString);
                $appointments = $this->appointmentRepository->findAppointmentsRatesByDateRange($start,$end);
                $maxValue = $this->findMaxValue($appointments);
                return [
                    'entries' => $appointments,
                    'max' => $maxValue
                ];
        }
        else {
            throw new \Exception("Start and end date must be provided");
        }

    }

    function findMaxValue($entries) {
        $maxValue = 0;

        foreach ($entries as $entry) {
            if ($entry['value'] > $maxValue) {
                $maxValue = $entry['value'];
            }
        }

        return $maxValue;
    }
}

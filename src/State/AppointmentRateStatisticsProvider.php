<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Provider;
use App\Repository\AppointmentRepository;
use Symfony\Bundle\SecurityBundle\Security;

class AppointmentRateStatisticsProvider implements ProviderInterface
{
    private AppointmentRepository $appointmentRepository;
    private Security $security;
    public function __construct(AppointmentRepository $appointmentRepository, Security $security)
    {
        $this->appointmentRepository = $appointmentRepository;
        $this->security = $security;
    }


    /**
     * @throws \Exception
     */
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

        $startString = $context['filters']['start'];
        $endString = $context['filters']['end'];
        $establishmentId = $context['filters']['establishmentId'] ?? null;

        if (!$startString || !$endString) {
            throw new \Exception('You must provide a start and end date');
        }

        if($isProvider && !$establishmentId && !$isBarber) {
            return $this->getAppointments($id, new \DateTime($startString), new \DateTime($endString));
        }
        else if ($isProvider && $establishmentId && !$isBarber) {
            return $this->getAppointments($id, new \DateTime($startString), new \DateTime($endString), $establishmentId);
        }
        else if($isBarber) {
            return $this->getAppointments($id, new \DateTime($startString), new \DateTime($endString), $establishmentId);
        }




        return null;
    }

    function getAppointments(string $id, \DateTime $start, \DateTime $end, string $establishmentId = null): array
    {
        if($establishmentId) {
            $appointments = $this->appointmentRepository->findAppointmentsRatesByDateRange($id,$start,$end,$establishmentId);
        } else {
            $appointments = $this->appointmentRepository->findAppointmentsRatesByDateRange($id,$start,$end);
        }
        $maxValue = $this->findMaxValue($appointments);
        return [
            'entries' => $appointments,
            'max' => $maxValue
        ];
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

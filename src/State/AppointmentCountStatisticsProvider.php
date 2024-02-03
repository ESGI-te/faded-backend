<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Provider;
use App\Repository\AppointmentRepository;
use Symfony\Bundle\SecurityBundle\Security;

class AppointmentCountStatisticsProvider implements ProviderInterface
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

        $days = (int) $context['filters']['days'];
        $establishmentId = $context['filters']['establishmentId'] ?? null;



        if($isProvider && $days && !$establishmentId && !$isBarber) {
            return $this->getAppointments($id,$days);
        }else if ($isProvider && $days && $establishmentId && !$isBarber) {
            return $this->getAppointments($id,$days,$establishmentId);
        }else if($isBarber && $days) {
            return $this->getAppointments($id,$days,$establishmentId);
        }


        return null;
    }

    private function getAppointments(int $id, int $days, string $establishmentId = null): array
    {
        if($establishmentId) {
            $appointments = $this->appointmentRepository->findByRangAppointments($id,$days,$establishmentId);
        }
        else {
            $appointments = $this->appointmentRepository->findByRangAppointments($id,$days);
        }

        return [
            'appointments' => $appointments
        ];
    }
}



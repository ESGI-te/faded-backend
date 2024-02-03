<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Provider;
use App\Repository\AppointmentRepository;
use Symfony\Bundle\SecurityBundle\Security;

class TopServicesProvider implements ProviderInterface
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

        $limit = $context['filters']['limit'];
        $establishmentId = $context['filters']['establishmentId'] ?? null;

        if(!$limit) {
            throw new \Exception('You must provide a limit');
        }

        if ($isProvider && !$establishmentId && !$isBarber) {
            return $this->appointmentRepository->findTopServicesByAppointmentCount($id,$limit);
        }
        else if ($isProvider && $establishmentId && !$isBarber) {
            return $this->appointmentRepository->findTopServicesByAppointmentCount($id,$limit,$establishmentId);
        }
        else if($isBarber) {
            return $this->appointmentRepository->findTopServicesByAppointmentCount($id,$limit,$establishmentId);
        }
        else {
            throw new \Exception("something went wrong");
        }
    }
}

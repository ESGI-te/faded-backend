<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Provider;
use App\Repository\AppointmentRepository;
use App\Repository\FeedbackRepository;
use Symfony\Bundle\SecurityBundle\Security;

class GlobalIndicatorProvider implements ProviderInterface
{
    private AppointmentRepository $appointmentRepository;
    private FeedbackRepository $feedbackRepository;
    private Security $security;

    public function __construct(FeedbackRepository $feedbackRepository,AppointmentRepository $appointmentRepository, Security $security)
    {
        $this->appointmentRepository = $appointmentRepository;
        $this->feedbackRepository = $feedbackRepository;
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
            return $this->getFinalResult($id);
        }
        elseif ($establishmentId && $isProvider && !$isBarber){
            return $this->getFinalResult($id,$establishmentId);
        }
        elseif($isBarber) {
            return $this->getFinalResult($id,$establishmentId);
        }
        else {
            throw new \Exception("something went wrong");
        }
    }

    private function getFinalResult($id,$establishmentId = null ): array
    {
        if($establishmentId){
            $globalTurnover = $this->appointmentRepository->findGlobalTurnOver($id,$establishmentId);
            $globalAppointments = $this->appointmentRepository->findGlobalAppointments($id,$establishmentId);
            $globalFeedback = $this->feedbackRepository->findAverageFeedbackForEstablishment($establishmentId);
        }else{
            $globalTurnover = $this->appointmentRepository->findGlobalTurnOver($id);
            $globalAppointments = $this->appointmentRepository->findGlobalAppointments($id);
            $globalFeedback = $this->feedbackRepository->findAverageFeedbackForProvider($id);
        }

        return [
            'turnover' => $globalTurnover,
            'appointments' => $globalAppointments,
            'feedback' => $globalFeedback
        ];

    }

}
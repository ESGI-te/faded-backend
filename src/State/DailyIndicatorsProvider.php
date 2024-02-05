<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Provider;
use App\Repository\AppointmentRepository;
use Symfony\Bundle\SecurityBundle\Security;

class DailyIndicatorsProvider implements ProviderInterface
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
            $todaysIndicators = $this->appointmentRepository->findDailyIndicators('today','tomorrow',$id,$establishmentId);
            $yesterdaysIndicators = $this->appointmentRepository->findDailyIndicators('today - 1 day','today',$id,$establishmentId);
        }else{
            $todaysIndicators = $this->appointmentRepository->findDailyIndicators('today','tomorrow',$id);
            $yesterdaysIndicators = $this->appointmentRepository->findDailyIndicators('today - 1 day','today',$id);
        }

        $noValueArray = [
            'value' => 0,
            'percentageChange' => 0
        ];

        return [
            'turnover' => isset($todaysIndicators['turnover']) ? [
                'value' => $todaysIndicators['turnover'],
                'percentageChange' => $this->getPercentageChange($yesterdaysIndicators['turnover'],$todaysIndicators['turnover']) . '%'
            ] : $noValueArray,
            'appointments' => isset($todaysIndicators['appointments']) ? [
                'value' => $todaysIndicators['appointments'],
                'percentageChange' => $this->getPercentageChange($yesterdaysIndicators['appointments'],$todaysIndicators['appointments']) . '%'
            ] : $noValueArray,
            'services' => isset( $todaysIndicators['services']) ? [
                'value' => $todaysIndicators['services'],
                'percentageChange' => $this->getPercentageChange($yesterdaysIndicators['services'],$todaysIndicators['services']) . '%'
            ] : $noValueArray,
        ];

    }


    public function getPercentageChange($old, $new): float {
        $change = $new - $old;
        return ($change / $old) * 100;
    }

}
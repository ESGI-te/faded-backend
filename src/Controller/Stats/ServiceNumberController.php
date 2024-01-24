<?php

namespace App\Controller\Stats;

use App\Entity\Provider;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

class ServiceNumberController
{
    public function __invoke(Provider $provider,#[MapQueryParameter] string $date_range): array
    {
       $appointments = $provider->getAppointments();

       switch ($date_range){
           case 7:
                $appointments = $appointments->filter(function($appointment){
                     return $appointment->getDateTime() > new \DateTime('-7 days');
                });
                break;
           case 30:
                $appointments = $appointments->filter(function($appointment){
                     return $appointment->getDateTime() > new \DateTime('-30 days');
                });
                break;
           case 90:
                $appointments = $appointments->filter(function($appointment){
                     return $appointment->getDateTime() > new \DateTime('-90 days');
                });
                break;
           case 365:
                $appointments = $appointments->filter(function($appointment){
                     return $appointment->getDateTime() > new \DateTime('-365 days');
                });
                break;
       }

       return [
           'appointments' => $appointments
       ];
    }
}
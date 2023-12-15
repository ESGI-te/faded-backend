<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Repository\AppointmentRepository;
use App\Repository\BarberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use \Symfony\Component\HttpFoundation\Request;

class CreateBarberController extends AbstractController
{
    public function __invoke(
        Appointment $appointment,
        Request $request,
        AppointmentRepository $appointmentRepository,
        BarberRepository $barberRepository
    ): ?Appointment
    {
        $content = json_decode($request->getContent(), true);
        $barber = $content['barber'] ?? null;
        $establishment = $content['establishment'];
        $dateTime = $content['dateTime'];

        $user = $this->getUser();

        $appointment->setUser($user);

       if(!$barber)
       {
           $availableBarbers = $barberRepository->findAvailableBarbers($establishment, $dateTime);

           if(!$availableBarbers) {
               $this->json(['status' => 400, 'data' => "No barber available"]);
           }

           $randomAvailableBarber = $availableBarbers[rand(0, count($availableBarbers))];
           $barberEntity = $barberRepository->find($randomAvailableBarber['id']);
           $appointment->setBarber($barberEntity);
       }

        return $appointment;
    }
}
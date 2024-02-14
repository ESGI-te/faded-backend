<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class BarberPlanningProcessor implements ProcessorInterface
{
    public function __construct(
        readonly EntityManagerInterface $entityManager,
        readonly Security $security,
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $establishment = $data->getEstablishment();

        if ($establishment) {
            $establishmentPlanning = $establishment->getPlanning();
            $barberPlanning = $data->getPlanning();

            if (!$this->isPlanningValid($barberPlanning, $establishmentPlanning)) {
                throw new BadRequestHttpException('Invalid barber planning, it should match establishment planning');
            }
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    private function isPlanningValid(array $barberPlanning, array $establishmentPlanning): bool {
        foreach ($barberPlanning as $day => $hours) {

            if (!$establishmentPlanning[$day]['isOpen']) {
                if ($hours['isOpen']) {
                    return false; // barber can't be working if establishment is closed
                } else {
                    continue;
                }
            }

            $barberOpen = new \DateTime($hours['open']);
            $barberClose = new \DateTime($hours['close']);
            $establishmentOpen = new \DateTime($establishmentPlanning[$day]['open']);
            $establishmentClose = new \DateTime($establishmentPlanning[$day]['close']);

            if ($barberOpen < $establishmentOpen || $barberClose > $establishmentClose) {
                return false; // barber's hours should be within establishment's hours
            }
        }

        return true; // barber's planning is valid
    }
}

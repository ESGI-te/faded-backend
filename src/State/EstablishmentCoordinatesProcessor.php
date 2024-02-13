<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use App\Entity\Establishment;
use App\Enum\EstablishmentStatusEnum;
use App\Service\GeocodingService;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Metadata\Operation;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class EstablishmentCoordinatesProcessor implements ProcessorInterface
{

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(readonly EntityManagerInterface $entityManager, readonly GeocodingService $geocodingService)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $address = $data->getAddress();

        if(!$address) return;

        $coordinates = $this->geocodingService::geocodeAddress($address);

        if(!isset($coordinates)) {
            throw new BadRequestHttpException("Invalid address, coordinates can't be found", null, 400);
        }

        $data->setLongitude($coordinates['lng']);
        $data->setLatitude($coordinates['lat']);

        $this->entityManager->persist($data);
        $this->entityManager->flush();

    }

}
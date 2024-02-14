<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\Establishment;
use App\Repository\ServiceRepository;
use ApiPlatform\State\ProcessorInterface;
use App\Service\GeocodingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class EstablishmentServiceProcessor implements ProcessorInterface
{

    public function __construct(
        readonly EntityManagerInterface $entityManager,
        readonly ServiceRepository $serviceRepository,
        readonly Security $security,
        readonly GeocodingService $geocodingService
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $service = $this->serviceRepository->find($data->getId());

//        $establishments = $service->getEstablishment();
//        $newEstablishments = $data->getEstablishment();
//
//        if(count($newEstablishments) > count($establishments)) {
//            $establishment = $newEstablishments->last();
//            $establishment->addServiceCategory($data->getCategory());
//            $this->entityManager->persist($establishment);
//            $this->entityManager->persist($data);
//        }
//        if(count($newEstablishments) < count($establishments)) {
//            $removedEstablishments = array_diff((array)$establishments, $newEstablishments);
//
//            foreach ($removedEstablishments as $removedEstablishment) {
//                $establishmentServices = $removedEstablishment->getServices();
//                $sameCategoryServices = $establishmentServices->filter(function($s) use ($service) {
//                    return $s->getCategory() === $service->getCategory() && $s->getId() !== $service->getId();
//                });
//
//                if (count($sameCategoryServices) === 0) {
//                    $removedEstablishment->removeServiceCategory($service->getCategory());
//                    $this->entityManager->persist($removedEstablishment);
//                }
//            }
//                $this->entityManager->persist($data);
//        }
//
//        $this->entityManager->flush();

    }
}

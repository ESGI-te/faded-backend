<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\Establishment;
use App\Repository\ProviderRepository;
use ApiPlatform\State\ProcessorInterface;
use App\Service\GeocodingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AddProviderProcessor implements ProcessorInterface
{

    public function __construct(
        readonly EntityManagerInterface $entityManager,
        readonly ProviderRepository $providerRepository,
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
        $provider = $this->providerRepository->findOneBy(['user' => $this->security->getUser()]);

        if (!$provider) {
            throw new NotFoundHttpException('Provider not found');
        }

        $data->setProvider($provider);

        $this->entityManager->persist($data);
        $this->entityManager->flush();

    }
}

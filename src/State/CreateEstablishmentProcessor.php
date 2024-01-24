<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Repository\ProviderRepository;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CreateEstablishmentProcessor implements ProcessorInterface
{

    private EntityManagerInterface $entityManager;
    private ProviderRepository $providerRepository;
    private Security $security;


    public function __construct(
        EntityManagerInterface $entityManager,
        ProviderRepository $providerRepository,
        Security $security
    )
    {
        $this->entityManager = $entityManager;
        $this->providerRepository = $providerRepository;
        $this->security = $security;
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

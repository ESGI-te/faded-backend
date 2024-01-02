<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\ResetPasswordToken;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ResetPasswordTokenProcessor implements ProcessorInterface
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $user = $this->userRepository->findOneBy(['email' => $data->getEmail()]);

        if (!$user) {
            throw new NotFoundHttpException('Email not associated with a user.', null, 404);
        }

        $data->setUser($user);

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}

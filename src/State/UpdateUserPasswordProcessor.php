<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\Auth\User;
use App\Entity\ResetPasswordToken;
use App\Repository\ResetPasswordTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UpdateUserPasswordProcessor implements ProcessorInterface
{
    private ResetPasswordTokenRepository $resetPasswordTokenRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ResetPasswordTokenRepository $resetPasswordTokenRepository, EntityManagerInterface $entityManager)
    {
        $this->resetPasswordTokenRepository = $resetPasswordTokenRepository;
        $this->entityManager = $entityManager;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data instanceof User) {
            $this->validateToken($data);
        }
    }
    private function validateToken(User $user): void
    {
        $token = $this->resetPasswordTokenRepository->findOneBy(['user' => $user]);

        if (!$token) {
            throw new NotFoundHttpException('Token not found.', null, 404);
        }

        if ($token->getExpiresAt() < new \DateTime()) {
            $this->removeToken($token);
            throw new BadRequestHttpException('Token expired.', null, 400);
        }
    }

    private function removeToken(ResetPasswordToken $token): void
    {
        $this->entityManager->remove($token);
        $this->entityManager->flush();
    }
}

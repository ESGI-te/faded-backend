<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\Auth\User;
use App\Enum\EmailSenderEnum;
use App\Repository\UserRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ResetPasswordTokenProcessor implements ProcessorInterface
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private \Twig\Environment $twig;
    private EmailService $emailService;


    public function __construct(UserRepository $userRepository, EmailService $emailService, EntityManagerInterface $entityManager, \Twig\Environment $twig)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->emailService = $emailService;
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

        $this->sendResetPasswordEmail($user, $data->getToken());
    }

    private function sendResetPasswordEmail(User $user, string $token): void
    {
        $email = $user->getEmail();
        $subject = "Configurez un mot de passe";
        $from = EmailSenderEnum::NO_REPLY->value;
        $content = $this->twig->render('email/reset_password.html.twig', [
            'name' => $user->getFirstName(),
            'token' => $token
        ]);

        try {
            $this->emailService->sendEmail($from, [$email], $subject, $content);
        } catch (\Exception $e) {
            throw new \Exception('Error: ' . $e->getMessage());
        }

    }
}

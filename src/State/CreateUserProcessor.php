<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\Auth\User;
use App\Enum\EmailSenderEnum;
use App\Service\EmailService;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;

final class CreateUserProcessor implements ProcessorInterface
{

    private \Twig\Environment $twig;
    private EmailService $emailService;
    private EntityManagerInterface $entityManager;


    public function __construct(EmailService $emailService, \Twig\Environment $twig, EntityManagerInterface $entityManager)
    {
        $this->twig = $twig;
        $this->emailService = $emailService;
        $this->entityManager = $entityManager;

    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
        $this->sendWelcomeEmail($data);
    }

    private function sendWelcomeEmail(User $user): void {
        $email = $user->getEmail();
        $from = EmailSenderEnum::NO_REPLY->value;

        if(in_array("ROLE_PROVIDER", $user->getRoles())) {
            $subject = "Bienvenue sur Barbers PRO";
            $content = $this->twig->render('email/welcome_provider.html.twig', [
                'name' => $user->getFirstName(),
            ]);
        } else {
            $subject = "Bienvenue sur Barbers";
            $content = $this->twig->render('email/welcome_user.html.twig', [
                'name' => $user->getFirstName(),
            ]);
        }

        try {
            $this->emailService->sendEmail($from, [$email], $subject, $content);
        } catch (\Exception $e) {
            throw new \Exception('Error: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\EventListener;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use ApiPlatform\Util\RequestAttributesExtractor;
use App\Entity\Auth\User;
use App\Entity\ResetPasswordToken;
use App\Enum\EmailSenderEnum;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResetPasswordSubscriber implements EventSubscriberInterface
{
    private EmailService $emailService;
    private \Twig\Environment $twig;

    public function __construct(EmailService $emailService, \Twig\Environment $twig)
    {
        $this->emailService = $emailService;
        $this->twig = $twig;
    }

    public function shouldSendEmail(ViewEvent $event): void {
        $request = $event->getRequest();

        if (!$request->isMethod('POST')) {
            return;
        }

        $attributes = RequestAttributesExtractor::extractAttributes($request);
        $attributesResource = $attributes['resource_class'];

        if (
            !isset($attributesResource) || $attributesResource !== ResetPasswordToken::class
        ) {
            return;
        }

        $resetPasswordToken = $event->getControllerResult();
        $user = $resetPasswordToken->getUser();
        $token = $resetPasswordToken->getToken();

        $this->sendResetPasswordEmail($user, $token);
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

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['shouldSendEmail', EventPriorities::POST_WRITE],
        ];
    }
}
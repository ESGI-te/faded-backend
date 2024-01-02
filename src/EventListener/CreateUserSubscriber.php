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

class CreateUserSubscriber implements EventSubscriberInterface
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
            !isset($attributesResource) || $attributesResource !== User::class
        ) {
            return;
        }

        $user = $event->getControllerResult();
        $this->sendWelcomeEmail($user);
    }

    private function sendWelcomeEmail($user): void {
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
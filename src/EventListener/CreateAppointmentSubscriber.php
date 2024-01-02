<?php

namespace App\EventListener;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use ApiPlatform\Util\RequestAttributesExtractor;
use App\Entity\Appointment;
use App\Entity\Auth\User;
use App\Enum\EmailSenderEnum;
use App\Service\EmailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CreateAppointmentSubscriber implements EventSubscriberInterface
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
            !isset($attributesResource) || $attributesResource !== Appointment::class
        ) {
            return;
        }

        $appointment = $event->getControllerResult();

        $this->sendAppointmentSummaryEmail($appointment);
    }

    private function sendAppointmentSummaryEmail(Appointment $appointment): void
    {
        $email = $appointment->getUser()->getEmail();
        $subject = "Votre RDV";
        $from = EmailSenderEnum::NO_REPLY->value;
        $content = $this->twig->render('email/appointment_summary.html.twig', [

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
<?php

namespace App\EventListener;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use ApiPlatform\Util\RequestAttributesExtractor;
use App\Enum\EmailSenderEnum;
use App\Service\EmailService;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class WelcomeEmailSubscriber implements EventSubscriberInterface
{
    private EmailService $emailService;
    private \Twig\Environment $twig;
    private string $content ;

    public function __construct(EmailService $emailService, \Twig\Environment $twig)
    {
        $this->twig = $twig;
        $this->emailService = $emailService;
    }

    /**
     * @throws Exception
     */
    public function sendWelcomeEmail(ViewEvent $event): void
    {

        $method = $event->getRequest()->getMethod();

        if ($method !== Request::METHOD_POST) {
            return;
        }

        $request = $event->getRequest();

        if ($request->isMethod('POST')) {

            $attributes = RequestAttributesExtractor::extractAttributes($request);
            $attributesResource = $attributes['resource_class'];
            if (
                isset($attributesResource) && $attributesResource === 'App\Entity\Auth\User'
            ) {
                $data = $event->getControllerResult();
                $email = $data->getEmail();
                $subject = "Bienvenue sur BarberShop ";
                $roles = $data->getRoles();

                if(in_array("ROLE_BARBER", $roles)) {
                    $this->content = $this->twig->render('email/welcome_barber.html.twig', [
                        'name' => $data->getFirstName(),
                    ]);
                }

                $from =EmailSenderEnum::WELCOME->value;

                try {
                    $this->emailService->sendEmail($from, [$email], $subject, $this->content);
                } catch (Exception $e) {
                    throw new Exception('Error: ' . $e->getMessage());
                }
            }
        }
    }
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['sendWelcomeEmail', EventPriorities::POST_WRITE],
        ];
    }
}
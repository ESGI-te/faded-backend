<?php

namespace App\EventListener;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use ApiPlatform\Util\RequestAttributesExtractor;
use App\Enum\EmailSenderEnum;
use App\Service\EmailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class WelcomeEmailSubscriber implements EventSubscriberInterface
{
    private EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

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

                if(in_array("ROLE_PROVIDER", $roles)) {
                    $content = "<b> Bienvenue sur BarberShop - Provider </b> <br> Merci de devinir un prestataire chez babrbers: <br> 
                            <a href='http://localhost:5173/login'>
                            Connexion</a>";
                } else {
                    $content = "<b> Bienvenue sur BarberShop </b> <br> Veuillez cliquer sur le lien suivant pour vous connecter : <br> 
                            <a href='http://localhost:5173/login'>
                            Connexion</a>";
                }


                $from =EmailSenderEnum::WELCOME->value;

                try {
                    $this->emailService->sendEmail($from, [$email], $subject, $content);
                } catch (\Exception $e) {
                    throw new \Exception('Error: ' . $e->getMessage());
                }

            }

        }

    }
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['sendWelcomeEmail', EventPriorities::POST_WRITE],
        ];
    }
}
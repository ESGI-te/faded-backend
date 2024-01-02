<?php

namespace App\Controller;

use App\Entity\ProviderRequest;
use App\Enum\EmailSenderEnum;
use App\Service\EmailService;
use Exception;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




class AcceptProviderController extends AbstractController
{

    /**
     * @throws RandomException
     * @throws Exception
     */

    public function __invoke(ProviderRequest $providerRequest, EmailService $emailService): ProviderRequest
    {
        $token = bin2hex(random_bytes(120));
        $providerRequest->setToken($token);
        $providerRequest->setTokenExpirationDate(new \DateTime('+1 day'));

        $ProviderEmail =  $providerRequest->getPersonalEmail();

        $subject = "Validation de votre inscription";

        $content = "Bonjour, <br> Veuillez cliquer sur le lien suivant pour valider votre inscription : <br> 
                    <a href='http://localhost:5173/provider-request/password-set?token=" . $token . "'>
                    Valider mon inscription</a>";

        $from = EmailSenderEnum::NO_REPLY->value;

        try {
            $emailService->sendEmail($from, [$ProviderEmail], $subject, $content);
            return $providerRequest;
        } catch (Exception $e) {
            throw new Exception('Error: ' . $e->getMessage());
        }
    }

}


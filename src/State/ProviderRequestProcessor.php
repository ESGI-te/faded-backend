<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Auth\User;
use App\Entity\Provider;
use App\Entity\ResetPasswordToken;
use App\Enum\EmailSenderEnum;
use App\Enum\ProviderRequestStatusEnum;
use App\Enum\RolesEnum;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProviderRequestProcessor implements ProcessorInterface
{

    private EntityManagerInterface $entityManager;
    private $faker;
    private \Twig\Environment $twig;
    private EmailService $emailService;


    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager,HttpClientInterface $client, \Twig\Environment $twig, EmailService $emailService)
    {
        $this->faker = Factory::create();
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->emailService = $emailService;

    }


    /**
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {

        if ($data->getStatus() === ProviderRequestStatusEnum::REJECTED->value) {
            $this->entityManager->persist($data);
            $this->entityManager->flush();
            return;
        }

        if($data->getStatus() !== ProviderRequestStatusEnum::APPROVED->value) return;

        try {
            $user = new User();
            $user->setFirstName($data->getFirstName());
            $user->setLastName($data->getLastName());
            $user->setEmail($data->getPersonalEmail());
            $user->setPassword($this->faker->password);
            $user->setRoles([RolesEnum::USER->value, RolesEnum::PROVIDER->value]);

            $provider = new Provider();
            $provider->setUser($user);
            $provider->setName($data->getCompanyName());
            $provider->setAddress($data->getCompanyAddress());
            $provider->setEmail($data->getProfessionalEmail());
            $provider->setPhone($data->getPhone());
            $provider->setKbis($data->getKbis());

            $resetPasswordToken = new ResetPasswordToken();
            $resetPasswordToken->setUser($user);

            $this->entityManager->persist($user);
            $this->entityManager->persist($provider);
            $this->entityManager->persist($resetPasswordToken);
            $this->entityManager->persist($data);
            $this->entityManager->flush();

            $this->sendResetPasswordEmail($user, $resetPasswordToken->getToken());
        } catch (\Exception $e) {
            throw new \Exception('Error: ' . $e->getMessage());
        }
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
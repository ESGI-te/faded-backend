<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Auth\User;
use App\Entity\Barber;
use App\Entity\ResetPasswordToken;
use App\Enum\EmailSenderEnum;
use App\Enum\RolesEnum;
use App\Repository\ProviderRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CreateBarberProcessor implements ProcessorInterface
{

    private EntityManagerInterface $entityManager;
    private \Faker\Generator $faker;
    private \Twig\Environment $twig;
    private EmailService $emailService;

    public function __construct(
        EntityManagerInterface $entityManager,
        HttpClientInterface $client,
        \Twig\Environment $twig,
        EmailService $emailService,
        readonly string $managerUrl,
        readonly ProviderRepository $providerRepository,
        readonly Security $security
    )
    {
        $this->faker = Factory::create();
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->emailService = $emailService;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $provider = $this->providerRepository->findOneBy(['user' => $this->security->getUser()]);

        if (!$provider) {
            throw new NotFoundHttpException('Provider not found');
        }
        try {

            $user = new User();
            $user->setFirstName($data->getFirstName());
            $user->setLastName($data->getLastName());
            $user->setEmail($data->getEmail());
            $user->setPassword($this->faker->password);
            $user->setRoles([RolesEnum::USER->value, RolesEnum::BARBER->value]);

            $data->setUser($user);
            $data->setProvider($provider);

            $resetPasswordToken = new ResetPasswordToken();
            $resetPasswordToken->setUser($user);

            $this->entityManager->persist($user);
            $this->entityManager->persist($resetPasswordToken);
            $this->entityManager->persist($data);
            $this->entityManager->flush();

            $this->sendResetPasswordEmail($data, $resetPasswordToken->getToken());
        } catch (\Exception $e) {
            throw new \Exception('Error: ' . $e->getMessage());
        }
    }

    private function sendResetPasswordEmail(Barber $barber, string $token): void
    {
        $link = $this->managerUrl . '/reset-password?token=' . $token;
        $email = $barber->getEmail();
        $subject = "Bienvenue sur Barbers";
        $from = EmailSenderEnum::NO_REPLY->value;

        $content = $this->twig->render('email/welcome_barber.html.twig', [
            'firstName' => $barber->getFirstName(),
            'email' => $email,
            'link' => $link,
            'providerName' => $barber->getProvider()->getName()
        ]);

        try {
            $this->emailService->sendEmail($from, [$email], $subject, $content);
        } catch (\Exception $e) {
            throw new \Exception('Error: ' . $e->getMessage());
        }
    }
}

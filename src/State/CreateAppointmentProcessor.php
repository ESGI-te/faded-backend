<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Entity\Appointment;
use App\Enum\EmailSenderEnum;
use App\Repository\BarberRepository;
use App\Service\EmailService;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;


final class CreateAppointmentProcessor implements ProcessorInterface
{

    private \Twig\Environment $twig;
    private EmailService $emailService;
    private EntityManagerInterface $entityManager;
    private BarberRepository $barberRepository;
    private Security $security;

    public function __construct(
        EmailService $emailService,
        \Twig\Environment $twig,
        EntityManagerInterface $entityManager,
        BarberRepository $barberRepository,
        Security $security
    )
    {
        $this->twig = $twig;
        $this->emailService = $emailService;
        $this->entityManager = $entityManager;
        $this->barberRepository = $barberRepository;
        $this->security = $security;
    }

    /**
     * @throws \Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if(!$data->getBarber())
        {
            $this->addRandomBarber($data);
        }

        $establishment = $data->getEstablishment();
        $provider = $establishment->getProvider();

        $data->setProvider($provider);

        $user = $this->security->getUser();
        $data->setUser($user);

        $this->entityManager->persist($data);
        $this->entityManager->flush();
        $this->sendAppointmentSummaryEmail($data);

        // Générer le QR code pour l'identifiant de l'objet Appointment
        $this->generateQRCode($data->getId());
    }

    private function addRandomBarber(Appointment $appointment): void
    {
        $availableBarbers = $this->barberRepository->findAvailableBarbers(
            $appointment->getEstablishment()->getId(),
            $appointment->getDateTime()->format('Y-m-d H:i:s')
        );

        if(!$availableBarbers) {
            throw new NotFoundHttpException("No barber available", null, 404);
        }

        $randomAvailableBarber = $availableBarbers[rand(0, count($availableBarbers))];
        $barberEntity = $this->barberRepository->find($randomAvailableBarber['id']);
        $appointment->setBarber($barberEntity);
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

    private function generateQRCode(string $appointmentId): void
    {
        // Création de l'objet QR Code
        $qrCode = Builder::create()
            ->data($appointmentId)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Low) 
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin) 
            ->foregroundColor(new Color(0, 0, 0))
            ->backgroundColor(new Color(255, 255, 255))
            ->build();

        // Obtenez la chaîne de caractères représentant le contenu du QR code
        $qrCodeString = $qrCode->getString();
    }
}

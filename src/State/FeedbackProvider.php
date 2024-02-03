<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Provider;
use App\Repository\AppointmentRepository;
use App\Repository\FeedbackRepository;
use Symfony\Bundle\SecurityBundle\Security;

class FeedbackProvider implements ProviderInterface
{
    private FeedbackRepository $feedbackRepository;
    private Security $security;

    public function __construct(FeedbackRepository $feedbackRepository, Security $security)
    {
        $this->feedbackRepository = $feedbackRepository;
        $this->security = $security;
    }


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        $provider = $user->getProvider();
        assert($provider instanceof Provider);
        $id = $provider->getId();

        $isProvider = $this->security->isGranted('ROLE_PROVIDER');
        $isBarber = $this->security->isGranted('ROLE_BARBER');

        if(!$isProvider && !$isBarber) {
            throw new \Exception('You do not have permission for this query');
        }

        $establishmentId = $context['filters']['establishmentId'] ?? null;

        if($isProvider && !$establishmentId) {
            return $this->feedbackRepository->findAverageFeedbackForProvider($id);
        }
        elseif($establishmentId && $isProvider || $isBarber && !$isProvider) {
            return $this->feedbackRepository->findAverageFeedbackForEstablishment($establishmentId);
        }
        else {
            throw new \Exception("something went wrong");
        }
    }
}

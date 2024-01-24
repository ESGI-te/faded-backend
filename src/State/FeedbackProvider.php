<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\AppointmentRepository;
use App\Repository\FeedbackRepository;

class FeedbackProvider implements ProviderInterface
{
    private FeedbackRepository $feedbackRepository;

    public function __construct(FeedbackRepository $feedbackRepository)
    {
        $this->feedbackRepository = $feedbackRepository;
    }


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $providerId = $context['filters']['providerId'];
        $establishmentId = $context['filters']['establishmentId'];

        if($providerId && !$establishmentId) {
            return $this->feedbackRepository->findAverageFeedbackForProvider($providerId);
        }
        elseif($establishmentId && $providerId || !$providerId && $establishmentId){
            return $this->feedbackRepository->findAverageFeedbackForEstablishment($establishmentId);
        }
        else {
            throw new \Exception("something went wrong");
        }
    }
}

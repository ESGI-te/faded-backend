<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\AppointmentCountStatisticsProvider;
use App\State\AppointmentRateStatisticsProvider;
use App\State\FeedbackProvider;
use App\State\TopServicesProvider;
use App\State\TurnOverProvider;

#[ApiResource(
    operations: [
        new Get(
        uriTemplate: '/statistics/appointments/count',
        provider: AppointmentCountStatisticsProvider::class),

        new Get(
            uriTemplate: '/statistics/appointments/rate',
            provider: AppointmentRateStatisticsProvider::class),

        new Get(
            uriTemplate: '/statistics/services/top',
            provider: TopServicesProvider::class),


        new Get(
            uriTemplate: '/statistics/turnover',
            provider: TurnOverProvider::class),

        new Get(
            uriTemplate: '/statistics/feedback/rate',
            provider: FeedbackProvider::class),
    ]


)]
class Statistics
{

}
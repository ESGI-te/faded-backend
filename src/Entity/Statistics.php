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
            openapiContext: [
                'summary' => 'Get the number of appointments',
                'description' => 'Get the number of appointments for the current provider or barber',
                'parameters' => [
                    [
                        'in' => 'query',
                        'name' => 'days',
                        'required' => true,
                        'schema' => [
                            'type' => 'integer',
                            'minimum' => 1,
                            'description' => 'The date to filter the appointments'
                        ],
                    ],
                    [
                        'in' => 'query',
                        'name' => 'establishmentId',
                        'required' => false,
                        'schema' => [
                            'type' => 'string',
                            'description' => 'The establishment id to filter the appointments'
                        ]
                    ],
                ],
            ],
            security: "is_granted('ROLE_PROVIDER') or is_granted('ROLE_BARBER')",
            provider: AppointmentCountStatisticsProvider::class
        ),

        new Get(
            uriTemplate: '/statistics/appointments/rate',
            openapiContext: [
                'summary' => 'Get the appointment success rate',
                'description' => 'Retrieve the success rate of appointments, calculating the ratio of completed appointments to total appointments for the current provider or establishment.',
                'parameters' => [
                    [
                        'in' => 'query',
                        'name' => 'start',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                            'description' => 'The start date to filter the appointments'
                        ]
                    ],
                    [
                        'in' => 'query',
                        'name' => 'end',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                            'description' => 'The end date to filter the appointments'
                        ]
                    ],
                    [
                        'in' => 'query',
                        'name' => 'establishmentId',
                        'required' => false,
                        'schema' => [
                            'type' => 'string',
                            'description' => 'The establishment id to filter the appointments'
                        ]
                    ],
                ],
            ],
            security: "is_granted('ROLE_PROVIDER') or is_granted('ROLE_BARBER')",
            provider: AppointmentRateStatisticsProvider::class),

        new Get(
            uriTemplate: '/statistics/services/top',
            openapiContext: [
                'summary' => 'Get top performed services',
                'description' => 'Lists the top performed services based on the number of times they have been provided by the current provider or establishment.',
                'parameters' => [
                    [
                        'in' => 'query',
                        'name' => 'limit',
                        'required' => true,
                        'schema' => [
                            'type' => 'integer',
                            'minimum' => 3,
                            'description' => 'The number of services to retrieve. Default is 3.'
                        ]
                    ],
                    [
                        'in' => 'query',
                        'name' => 'establishmentId',
                        'required' => false,
                        'schema' => [
                            'type' => 'string',
                            'description' => 'The establishment id to filter the appointments'
                        ]
                    ],
                ],
            ],
            security: "is_granted('ROLE_PROVIDER') or is_granted('ROLE_BARBER')",
            provider: TopServicesProvider::class),


        new Get(
            uriTemplate: '/statistics/turnover',
            openapiContext: [
                'summary' => 'Get turnover statistics',
                'description' => 'Retrieve turnover statistics for the current provider or establishment',
                'parameters' => [
                    [
                        'in' => 'query',
                        'name' => 'establishmentId',
                        'required' => false,
                        'schema' => [
                            'type' => 'string',
                            'description' => 'The establishment id to filter the appointments'
                        ]
                    ],
                ],
            ],
            security: "is_granted('ROLE_PROVIDER') or is_granted('ROLE_BARBER')",
            provider: TurnOverProvider::class),

        new Get(
            uriTemplate: '/statistics/feedback/rate',
            openapiContext: [
                'summary' => 'Get feedback rating average',
                'description' => 'Calculate the average feedback rating from clients for the services provided by the current provider or establishment.',
                'parameters' => [
                    [
                        'in' => 'query',
                        'name' => 'establishmentId',
                        'required' => false,
                        'schema' => [
                            'type' => 'string',
                            'description' => 'The establishment id to filter the appointments'
                        ]
                    ],
                ],
            ],
            security: "is_granted('ROLE_PROVIDER') or is_granted('ROLE_BARBER')",
            provider: FeedbackProvider::class),
    ]


)]
class Statistics
{

}
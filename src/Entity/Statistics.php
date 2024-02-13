<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\AdminIndicatorsProvider;
use App\State\AppointmentCountStatisticsProvider;
use App\State\AppointmentRateStatisticsProvider;
use App\State\DailyIndicatorsProvider;
use App\State\FeedbackProvider;
use App\State\GlobalIndicatorProvider;
use App\State\NewUserByDateRangeProvider;
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
        new Get(
            uriTemplate: '/statistics/daily',
            openapiContext: [
                'summary' => 'Get daily indicators',
                'description' => 'Calculate daily turnover, number of appointments and number of services made for the current provider or establishment.',
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
            provider: DailyIndicatorsProvider::class),
        new Get(
            uriTemplate: '/statistics/global',
            openapiContext: [
                'summary' => 'Get global indicators',
                'description' => 'Calculate global turnover, number of appointments and feedback for the current provider or establishment.',
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
            provider: GlobalIndicatorProvider::class),
        new Get(
            uriTemplate: '/statistics/admin/indicators',
            openapiContext: [
                'summary' => 'Get global indicators',
                'description' => ' Calculate global indicators for admin new users, total count of providers and daily cash flow. ',
            ],
            security: "is_granted('ROLE_ADMIN')",
            provider: AdminIndicatorsProvider::class),
        new Get(
            uriTemplate: '/statistics/admin/userTraffic',
            openapiContext: [
                'summary' => 'Get user traffic by date rang',
                'description' => ' Calculate user traffic by date range for admin.',
                'parameters' => [
                    [
                        'in' => 'query',
                        'name' => 'start',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                            'description' => 'The start date to filter the creation date of the users'
                        ]
                    ],
                    [
                        'in' => 'query',
                        'name' => 'end',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                            'description' => 'The end date to filter the creation date of the users'
                        ]
                    ],
                ],
            ],
            security: "is_granted('ROLE_ADMIN')",
            provider: NewUserByDateRangeProvider::class),
    ]


)]
class Statistics
{

}
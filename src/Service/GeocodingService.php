<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class GeocodingService
{
    protected static string $apiUrl = 'https://api.opencagedata.com/geocode/v1/json';
    protected static string $apiKey;

    public function __construct(string $apiKey)
    {
        self::$apiKey = $apiKey;
    }

    static function geocodeAddress(string $address) : array
    {
        $httpClient = HttpClient::create();

        $response = $httpClient->request('GET', self::$apiUrl, [
            'query' => [
                'q' => $address,
                'key' => self::$apiKey,
            ],
        ]);

        $data = $response->toArray();

        if ($data['results'] && !empty($data['results'][0]['geometry'])) {
            $coordinates = $data['results'][0]['geometry'];
            return [
                'lat' => $coordinates['lat'],
                'lng' => $coordinates['lng'],
            ];
        } else {
            return [];
        }
    }
}
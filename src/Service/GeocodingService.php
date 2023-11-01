<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class GeocodingService
{
    private string $apiUrl = 'https://api.opencagedata.com/geocode/v1/json';

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function geocodeAddress(string $address) : array
    {
        $httpClient = HttpClient::create();

        $response = $httpClient->request('GET', $this->apiUrl, [
            'query' => [
                'q' => $address,
                'key' => $this->apiKey,
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

    function haversineDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Radius of the Earth in kilometers
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;
        return $distance;
    }
}
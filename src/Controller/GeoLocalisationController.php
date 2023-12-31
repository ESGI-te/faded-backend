<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\EstablishmentRepository;
use App\Repository\FeedbackRepository;
use App\Repository\ServiceRepository;
use App\Service\GeocodingService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use function MongoDB\BSON\toJSON;

class GeoLocalisationController
{
    public function __invoke(
        GeocodingService $geocodingService,
        EstablishmentRepository $establishmentRepository,
        #[MapQueryParameter] string $address,
        #[MapQueryParameter] string $categoryId,
        #[MapQueryParameter] string $radius,
    ): JsonResponse {

        $clientCoordinates = $geocodingService->geocodeAddress($address);
        $response = new JsonResponse();

        if (!$clientCoordinates) {
            return $response->setData(['status' => 400, 'data' => "no coordinates"]);
        }

        $establishments = $establishmentRepository->findAllNearToAdress($clientCoordinates['lat'], $clientCoordinates['lng'], $radius);

        $sortedEstablishments = [];

        foreach ($establishments as $establishment) {

            $categories = $establishment['category'];
            $uuidArray = explode(',', str_replace(['{', '}'], '', $categories));
            $uuidArray = array_map('trim', $uuidArray);

            if ($categoryId) {
                foreach ($uuidArray as $cat) {
                    if ($cat == $categoryId)
                        $sortedEstablishments[] = [
                            'id' => $establishment['id'],
                            'name' => $establishment['name'],
                            'address' => $establishment['address'],
                            'distance' => floatval($establishment['distance']),
                            'note' => floatval($establishment['moyenne']),
                            'note_count' => $establishment['note_count'],
                            'lat' => floatval($establishment['latitude']),
                            'lng' => floatval($establishment['longitude'])
                        ];
                }
            } else {
                $sortedEstablishments[] = [
                    'id' => $establishment['id'],
                    'name' => $establishment['name'],
                    'address' => $establishment['address'],
                    'distance' => floatval($establishment['distance']),
                    'note' => floatval($establishment['moyenne']),
                    'note_count' => $establishment['note_count'],
                    'lat' => floatval($establishment['latitude']),
                    'lng' => floatval($establishment['longitude'])
                ];
            }
        }


        return $response->setData($sortedEstablishments);
    }
}

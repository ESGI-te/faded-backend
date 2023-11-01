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
        ServiceRepository $serviceRepository,
        FeedbackRepository $feedbackRepository,
        #[MapQueryParameter] string $address,
        #[MapQueryParameter] string $categoryId,
        #[MapQueryParameter] string $rayon,
    ):JsonResponse
    {

        $clientCoordinates = $geocodingService->geocodeAddress($address);
        $response = new JsonResponse();

        if (!$clientCoordinates) {
            return $response->setData(['status' => 400, 'data' => "no coordinates"]);
        }

        $establishments = $establishmentRepository->findAllNearToAdress($clientCoordinates['lat'],$clientCoordinates['lng'],$rayon);

        $sortedEstablishments = [];

        foreach ($establishments as $establishment) {

                $categories = $establishment['category'];
                $uuidArray = explode(',', str_replace(['{', '}'], '', $categories));
                $uuidArray = array_map('trim', $uuidArray);

                if($categoryId){
                    foreach ($uuidArray as $cat) {
                        if ($cat == $categoryId)
                            $sortedEstablishments[] = [
                                'establishment_name' => $establishment['name'],
                                'establishment_adress' => $establishment['address'],
                                'distance' => number_format($establishment['distance'],2),
                                'note' => number_format($establishment['moyenne'],2),
                                'note_count' => $establishment['note_count'],
                            ];
                    }}
                else{
                    $sortedEstablishments[] = [
                        'establishment_name' => $establishment['name'],
                        'establishment_adress' => $establishment['address'],
                        'distance' => number_format($establishment['distance'],2),
                        'note' => number_format($establishment['moyenne'],2),
                        'note_count' => $establishment['note_count'],
                    ];
                }

        }


        return $response->setData(['status' => 200, 'nearest-establishsment' => $sortedEstablishments]);
    }


    #[Route('to-adress', name: 'location-to-adress', methods: ['GET'])]
    public function geocodeAction(GeocodingService $geocodingService):JsonResponse
    {
        $address = 'Tour Eiffel'; // Replace with the address you want to geocode
        $coordinates = $geocodingService->geocodeAddress($address);
        $response = new JsonResponse();
        if ($coordinates) {
            // Latitude and longitude coordinates
            $latitude = $coordinates['lat'];
            $longitude = $coordinates['lng'];


            $full_localisation = $latitude . ' ' . $longitude;

            return $response->setData(['status' => 200, $coordinates]);


        } else {
            return $response->setData(['status' => 400, 'data' => "something went wrong"]);
        }

    }
}
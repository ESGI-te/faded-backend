<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Service\GeocodingService;
use Doctrine\ORM\QueryBuilder;

const RADIUS = 10;

final class EstablishmentFilter extends AbstractFilter
{
    protected function filterProperty(
        string $property,
               $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {

        if (
            !$this->isPropertyEnabled($property, $resourceClass)
        ) {
            return;
        }

        $parameterName = $queryNameGenerator->generateParameterName($property);
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder->select('
            ' . $rootAlias . '.id,
            ' . $rootAlias . '.name,
            ' . $rootAlias . '.address,
            ' . $rootAlias . '.longitude,
            ' . $rootAlias . '.latitude,
            ' . $rootAlias . '.cover,
            (
                SELECT AVG(feedback1.note)
                FROM App\Entity\Feedback feedback1
                WHERE feedback1.establishment = ' . $rootAlias . '
            ) AS note,
            (
                SELECT COUNT(feedback2.note)
                FROM App\Entity\Feedback feedback2
                WHERE feedback2.establishment = ' . $rootAlias . '
            ) AS note_count,
            (6371 * ACOS(
                COS(RADIANS(:lat)) * COS(RADIANS(' . $rootAlias . '.latitude)) * COS(RADIANS(' . $rootAlias . '.longitude) - RADIANS(:lng)) +
                SIN(RADIANS(:lat)) * SIN(RADIANS(' . $rootAlias . '.latitude))
            )) AS distance
        ');

        switch ($property) {
            case 'address':
                $this->filterByLocation($queryBuilder, $value);
                break;
            case 'serviceCategories':
                if(strlen($property) < 1) {
                    break;
                }
                $this->filterByCategory($queryBuilder, $parameterName, $value);
                break;
            default:
                break;
        }
    }

    private function filterByLocation(QueryBuilder $queryBuilder, $value): void
    {
        $geocodingService = new GeocodingService($_ENV['GEOCODE_API_KEY']);
        $coordinates = $geocodingService::geocodeAddress($value);
        $longitude = $coordinates['lng'];
        $latitude = $coordinates['lat'];
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere('(6371 * ACOS(COS(RADIANS(:lat)) * COS(RADIANS(' . $rootAlias . '.latitude)) * COS(RADIANS(' . $rootAlias . '.longitude) - RADIANS(:lng)) + SIN(RADIANS(:lat)) * SIN(RADIANS(' . $rootAlias . '.latitude)))) <= :radius')
            ->orderBy('distance')
            ->setParameter('lat', $latitude)
            ->setParameter('lng', $longitude)
            ->setParameter('radius', RADIUS);
    }

    private function filterByCategory(QueryBuilder $queryBuilder, string $parameterName, $value): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->join($rootAlias . '.services', 's')
            ->join('s.category', 'c')
            ->andWhere('c.id = :' . $parameterName)
            ->setParameter($parameterName, $value)
            ->distinct();
    }

    public function getDescription(string $resourceClass): array
    {
        return [];
    }
}

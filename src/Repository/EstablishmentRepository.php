<?php

namespace App\Repository;

use App\Entity\Establishment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;


/**
 * @extends ServiceEntityRepository<Establishment>
 *
 * @method Establishment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Establishment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Establishment[]    findAll()
 * @method Establishment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstablishmentRepository extends ServiceEntityRepository
{
    private $connection;

    public function __construct(ManagerRegistry $registry,Connection $connection)
    {
        parent::__construct($registry, Establishment::class);
        $this->connection = $connection;
    }

//    /**
//     * @return Establishment[] Returns an array of Establishment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findAllNearToAdress($lat, $lng, $radius)
    {
        $sql_query = 'SELECT
                            e.id,e.name as name , e.address,e.latitude,e.longitude,
                            (
                                SELECT ARRAY_AGG(sr.id)
                                FROM service_category_establishment sc
                                         LEFT JOIN service_category sr ON sc.service_category_id = sr.id
                                WHERE sc.establishment_id = e.id
                            ) AS Category,
                            (
                                SELECT avg(f.note)
                                FROM feedback f
                                WHERE f.establishment_id = e.id
                            ) AS moyenne,
                            (
                                SELECT count(f.note)
                                FROM feedback f
                                WHERE f.establishment_id = e.id
                            ) AS note_count,
                            (6371 * ACOS(
                                            COS(RADIANS(:lat)) * COS(RADIANS(e.latitude)) * COS(RADIANS(e.longitude) - RADIANS(:lng)) +
                                            SIN(RADIANS(:lat)) * SIN(RADIANS(e.latitude))
                                    )) AS distance
                        FROM
                            establishment e
                        WHERE
                                (6371 * ACOS(
                                                COS(RADIANS(:lat)) * COS(RADIANS(e.latitude)) * COS(RADIANS(e.longitude) - RADIANS(:lng)) +
                                                SIN(RADIANS(:lat)) * SIN(RADIANS(e.latitude))
                                        )) <= :radius
                        ORDER BY distance';

        $params = [
            'lat' => $lat,
            'lng' => $lng,
            'radius' => $radius,
        ];

        return $this->connection->executeQuery($sql_query, $params)->fetchAll();

    }
}

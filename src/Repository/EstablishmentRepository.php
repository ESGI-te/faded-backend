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


}

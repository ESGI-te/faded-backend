<?php

namespace App\Repository;

use App\Entity\ProviderRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProviderRequest>
 *
 * @method ProviderRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProviderRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProviderRequest[]    findAll()
 * @method ProviderRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProviderRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProviderRequest::class);
    }

//    /**
//     * @return ProviderRequest[] Returns an array of ProviderRequest objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProviderRequest
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

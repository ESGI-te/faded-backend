<?php

namespace App\Repository;

use App\Entity\WeeklyOpeningHours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WeeklyOpeningHours>
 *
 * @method WeeklyOpeningHours|null find($id, $lockMode = null, $lockVersion = null)
 * @method WeeklyOpeningHours|null findOneBy(array $criteria, array $orderBy = null)
 * @method WeeklyOpeningHours[]    findAll()
 * @method WeeklyOpeningHours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeeklyOpeningHoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WeeklyOpeningHours::class);
    }

//    /**
//     * @return WeeklyOpeningHours[] Returns an array of WeeklyOpeningHours objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?WeeklyOpeningHours
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

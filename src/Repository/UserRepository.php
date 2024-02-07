<?php

namespace App\Repository;

use App\Entity\Auth\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findNewUsers()
    {
        return $this->createQueryBuilder('u')
            ->where('u.createdAt >= :startOfDay')
            ->andWhere('u.createdAt < :startOfNextDay')
            ->setParameter('startOfDay', new \DateTimeImmutable('today'), \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE)
            ->setParameter('startOfNextDay', new \DateTimeImmutable('tomorrow'), \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findNewUsersByDateRange(\DateTime $startDate, \DateTime $endDate, string $establishmentId = null): array
    {
        $qb = $this->createQueryBuilder('u')
            ->select('DATE(u.createdAt) as date, COUNT(u) as value')
            ->where('u.createdAt >= :start')
            ->andWhere('u.createdAt < :end')
            ->setParameter('start', $startDate->format('Y-m-d'))
            ->setParameter('end', $endDate->format('Y-m-d'))
            ->groupBy('date')
            ->orderBy('date', 'ASC');

        return $qb->getQuery()->getResult();
    }
}

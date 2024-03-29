<?php

namespace App\Repository;

use App\Entity\Feedback;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Feedback>
 *
 * @method Feedback|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feedback|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feedback[]    findAll()
 * @method Feedback[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedbackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feedback::class);
    }

//    /**
//     * @return Feedback[] Returns an array of Feedback objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findAverageFeedbackForProvider(string $providerId): float
    {
        $qb = $this->createQueryBuilder('f')
            ->select('AVG(f.note) AS averageNote')
            ->where('f.provider = :providerId')
            ->setParameter('providerId', $providerId);

        $result = $qb->getQuery()->getSingleScalarResult();
        return floatval(number_format((float)$result, 2, '.', ''));
    }

    public function findAverageFeedbackForEstablishment(string $establishmentId): float
    {
        $qb = $this->createQueryBuilder('f')
            ->select('AVG(f.note) AS averageNote')
            ->where('f.establishment = :establishmentId')
            ->setParameter('establishmentId', $establishmentId);

        $result = $qb->getQuery()->getSingleScalarResult();

        return floatval(number_format((float)$result, 2, '.', ''));
    }

}

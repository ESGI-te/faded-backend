<?php

namespace App\Repository;

use App\Entity\Appointment;
use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointment>
 *
 * @method Appointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appointment[]    findAll()
 * @method Appointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

//    /**
//     * @return Appointment[] Returns an array of Appointment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findByRangAppointments(string $provider,int $range, string $establishmentId = null): int
    {

        $start = (new \DateTime('-'.$range.' days'))->setTime(0, 0);
        $end = (new \DateTime())->setTime(23, 59, 59);

        $startString = $start->format('Y-m-d H:i:s');
        $endString = $end->format('Y-m-d H:i:s');

        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where('a.dateTime BETWEEN :start AND :end')
            ->andWhere('a.provider = :provider')
            ->setParameter('provider', $provider)
            ->setParameter('start', $startString)
            ->setParameter('end', $endString);

        if($establishmentId) {
            $qb->andWhere('a.establishment = :establishmentId')
                ->setParameter('establishmentId', $establishmentId);
        }

        try {
            $result = $qb->getQuery()->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return 0;
        }

        return (int) $result;
    }

    public function findAppointmentsRatesByDateRange(string $provider,\DateTime $startDate, \DateTime $endDate, string $establishmentId = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('DATE(a.dateTime) as date, COUNT(a) as value')
            ->where('a.dateTime >= :start')
            ->andWhere('a.dateTime < :end')
            ->andWhere('a.provider = :provider')
            ->setParameter('provider', $provider)
            ->setParameter('start', $startDate->format('Y-m-d'))
            ->setParameter('end', $endDate->format('Y-m-d'))
            ->groupBy('date')
            ->orderBy('date', 'ASC');

        if($establishmentId) {
            $qb->andWhere('a.establishment = :establishmentId')
                ->setParameter('establishmentId', $establishmentId);
        }

        return $qb->getQuery()->getResult();
    }

    public function findTopServicesByAppointmentCount(string $provider, int $limit,string $establishmentId = null): array
    {
        $qb =  $this->createQueryBuilder('a')
            ->select('IDENTITY(a.service) as id', 's.name', 'COUNT(a) as number', 's.price * COUNT(a) as turnover')
            ->andWhere('a.provider = :provider')
            ->setParameter('provider', $provider)
            ->join('a.service', 's')
            ->groupBy('a.service', 's.name', 's.price')
            ->orderBy('number', 'DESC')
            ->setMaxResults($limit);

          if($establishmentId) {
              $qb->andWhere('a.establishment = :establishmentId')
                  ->setParameter('establishmentId', $establishmentId);
              }

        return $qb->getQuery()->getResult();
    }

    public function findGlobalTurnOver(string $providerId,string $establishmentId = null): ?int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('SUM(s.price) AS turnover')
            ->join('a.service', 's')
            ->where('a.provider = :providerId')
            ->andWhere('a.status = :status')
            ->setParameter('providerId', $providerId)
            ->setParameter('status', 'planned');

        if($establishmentId) {
            $qb->andWhere('a.establishment = :establishmentId')
                ->setParameter('establishmentId', $establishmentId);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findDailyIndicators(string $start,string $end,string $providerId,string $establishmentId = null): array
    {

        $startOfDay = new \DateTime($start);

        $endOfDay = new \DateTime($end);

        $qb = $this->createQueryBuilder('a')
            ->select('SUM(s.price) AS turnover', 'COUNT(a) AS appointments', 'COUNT(s) AS services')
            ->join('a.service', 's')
            ->where('a.provider = :providerId')
            ->andWhere('a.status = :status')
            ->andWhere('a.dateTime >= :startOfDay AND a.dateTime < :endOfDay')
            ->setParameter('providerId', $providerId)
            ->setParameter('status', 'planned')
            ->setParameter('startOfDay', $startOfDay)
            ->setParameter('endOfDay', $endOfDay)
            ->groupBy('a.provider');

        if($establishmentId) {
            $qb->andWhere('a.establishment = :establishmentId')
                ->setParameter('establishmentId', $establishmentId);
        }

        if ($qb->getQuery()->getResult())
        {
            return $qb->getQuery()->getSingleResult();
        }

        return [];
    }

    public function findGlobalAppointments($id, string $establishmentId = null):int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where('a.provider = :id')
            ->andWhere('a.status = :status')
            ->setParameter('id', $id)
            ->setParameter('status', 'planned');

        if($establishmentId) {
            $qb->andWhere('a.establishment = :establishmentId')
                ->setParameter('establishmentId', $establishmentId);
        }

        try {
            $result = $qb->getQuery()->getSingleScalarResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return 0;
        }

        return (int) $result;
    }

    public function findAdminDailyIndicators(): int
    {

        $today = new \DateTime('today');
        $tomorrow = new \DateTime('today +1 day');

        $qb = $this->createQueryBuilder('a')
            ->select('SUM(s.price) AS cash_flow')
            ->join('a.service', 's')
            ->andWhere('a.status = :status')
            ->andWhere('a.dateTime >= :today AND a.dateTime < :tomorrow')
            ->setParameter('status', 'planned')
            ->setParameter('today', $today)
            ->setParameter('tomorrow', $tomorrow);

        if ($qb->getQuery()->getResult())
        {
            return $qb->getQuery()->getSingleScalarResult();
        }

        return 0;
    }

}

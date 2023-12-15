<?php

namespace App\Repository;

use App\Entity\Appointment;
use App\Entity\Barber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Barber>
 *
 * @method Barber|null find($id, $lockMode = null, $lockVersion = null)
 * @method Barber|null findOneBy(array $criteria, array $orderBy = null)
 * @method Barber[]    findAll()
 * @method Barber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BarberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Barber::class);
    }

    /**
     * @param string $establishmentId
     * @param string $dateTime
     * @return Barber[] Returns an array of Barber objects
     */
    public function findAvailableBarbers(string $establishmentId, string $dateTime): array
    {
        $qb = $this->createQueryBuilder('b');

        $qb->select('b.id', 'b.firstName', 'b.lastName', 'a.dateTime')
            ->leftJoin(Appointment::class, 'a', 'WITH', 'a.barber = b.id')
            ->where('b.establishment = :establishmentId')
            ->andWhere($qb->expr()->neq('a.dateTime', ':dateTime') . ' OR a.dateTime IS NULL') // Ajoutez cette condition pour inclure les barbiers sans rendez-vous
            ->setParameters([
                'establishmentId' => $establishmentId,
                'dateTime' => $dateTime,
            ]);

        return $qb->getQuery()->getResult();
    }
}

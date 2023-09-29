<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Booking;
use App\Domain\Repository\BookingRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Booking>
 *
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository implements BookingRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function save(Booking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findBusyTimes($date): array
    {
        return $this->createQueryBuilder('b')
            ->select('r.id')
            ->leftJoin('b.receptionHours', 'r')
            ->andWhere('b.date = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult()
        ;
    }
}

<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\ReceptionHours;
use App\Domain\Repository\ReceptionHoursRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReceptionHours>
 *
 * @method ReceptionHours|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceptionHours|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceptionHours[]    findAll()
 * @method ReceptionHours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceptionHoursRepository extends ServiceEntityRepository implements ReceptionHoursRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReceptionHours::class);
    }

    public function findFreeDates($busyTimes): array
   {
        $qb = $this->createQueryBuilder('r')
           ->select('r.time');
           if($busyTimes) {
            $qb
                ->andWhere('r.id NOT IN (:ids)')
                ->setParameter('ids', $busyTimes);
           }
           $query = $qb->getQuery();
           
       return $query->execute();
   }
}

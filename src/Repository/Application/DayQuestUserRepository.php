<?php

namespace App\Repository\Application;

use App\Entity\Application\DayQuestUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DayQuestUser>
 */
class DayQuestUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DayQuestUser::class);
    }

    public function isQuestCompletedByUser($userId, $questId): bool
    {
        $result = $this->createQueryBuilder('dqu')
            ->where('dqu.user = :userId')
            ->andWhere('dqu.dayQuest = :questId')
            ->setParameter('userId', $userId)
            ->setParameter('questId', $questId)
            ->getQuery()
            ->getOneOrNullResult();

        // Retourner true si la quête est marquée comme terminée, sinon false
        return $result !== null && $result->getEtat() === 1;
    }

    //    /**
    //     * @return DayQuestUser[] Returns an array of DayQuestUser objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?DayQuestUser
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

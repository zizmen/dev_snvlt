<?php

namespace App\Repository\References;

use App\Entity\References\Canrtonnement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Canrtonnement>
 *
 * @method Canrtonnement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Canrtonnement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Canrtonnement[]    findAll()
 * @method Canrtonnement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CanrtonnementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Canrtonnement::class);
    }

//    /**
//     * @return Canrtonnement[] Returns an array of Canrtonnement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Canrtonnement
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

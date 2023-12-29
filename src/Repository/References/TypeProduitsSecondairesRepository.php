<?php

namespace App\Repository\References;

use App\Entity\References\TypeProduitsSecondaires;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeProduitsSecondaires>
 *
 * @method TypeProduitsSecondaires|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeProduitsSecondaires|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeProduitsSecondaires[]    findAll()
 * @method TypeProduitsSecondaires[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeProduitsSecondairesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeProduitsSecondaires::class);
    }

//    /**
//     * @return TypeProduitsSecondaires[] Returns an array of TypeProduitsSecondaires objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TypeProduitsSecondaires
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

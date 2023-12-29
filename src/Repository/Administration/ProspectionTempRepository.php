<?php

namespace App\Repository\Administration;

use App\Entity\Administration\ProspectionTemp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProspectionTemp>
 *
 * @method ProspectionTemp|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProspectionTemp|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProspectionTemp[]    findAll()
 * @method ProspectionTemp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProspectionTempRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProspectionTemp::class);
    }

//    /**
//     * @return ProspectionTemp[] Returns an array of ProspectionTemp objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProspectionTemp
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

<?php

namespace App\Repository\References;

use App\Entity\References\NatureProduitSecondaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NatureProduitSecondaire>
 *
 * @method NatureProduitSecondaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method NatureProduitSecondaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method NatureProduitSecondaire[]    findAll()
 * @method NatureProduitSecondaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NatureProduitSecondaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NatureProduitSecondaire::class);
    }

//    /**
//     * @return NatureProduitSecondaire[] Returns an array of NatureProduitSecondaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NatureProduitSecondaire
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

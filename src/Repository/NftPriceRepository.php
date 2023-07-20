<?php

namespace App\Repository;

use App\Entity\NftPrice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NftPrice>
 *
 * @method NftPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method NftPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method NftPrice[]    findAll()
 * @method NftPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NftPriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NftPrice::class);
    }

//    /**
//     * @return NftPrice[] Returns an array of NftPrice objects
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

//    public function findOneBySomeField($value): ?NftPrice
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

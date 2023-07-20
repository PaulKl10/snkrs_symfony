<?php

namespace App\Repository;

use App\Entity\PurchaseNft;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PurchaseNft>
 *
 * @method PurchaseNft|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchaseNft|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchaseNft[]    findAll()
 * @method PurchaseNft[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchaseNftRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchaseNft::class);
    }

//    /**
//     * @return PurchaseNft[] Returns an array of PurchaseNft objects
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

//    public function findOneBySomeField($value): ?PurchaseNft
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

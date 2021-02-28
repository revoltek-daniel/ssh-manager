<?php

namespace App\Repository;

use App\Entity\SshKey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SshKey|null find($id, $lockMode = null, $lockVersion = null)
 * @method SshKey|null findOneBy(array $criteria, array $orderBy = null)
 * @method SshKey[]    findAll()
 * @method SshKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SshKeyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SshKey::class);
    }

    // /**
    //  * @return SshKey[] Returns an array of SshKey objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SshKey
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

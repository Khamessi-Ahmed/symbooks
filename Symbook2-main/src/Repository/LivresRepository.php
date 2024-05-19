<?php

namespace App\Repository;

use App\Entity\Livres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livres>
 *
 * @method Livres|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livres|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livres[]    findAll()
 * @method Livres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livres::class);
    }

//    /**
//     * @return Livres[] Returns an array of Livres objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Livres
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function findMostSoldBooks(): array
{
    return $this->createQueryBuilder('l')
        ->select('l.titre, SUM(l.Qte) as sales')
        ->groupBy('l.titre')
        ->orderBy('sales', 'DESC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult();
}


public function search($titre, $auteur)
    {
        $queryBuilder = $this->createQueryBuilder('l');

        if ($titre) {
            $queryBuilder->andWhere('l.titre LIKE :titre')
                         ->setParameter('titre', '%' . $titre . '%');
        }

        if ($auteur) {
            $queryBuilder->andWhere('l.Auteur LIKE :auteur')
                         ->setParameter('auteur', '%' . $auteur . '%');
        }

        return $queryBuilder->getQuery()->getResult();
    }

}

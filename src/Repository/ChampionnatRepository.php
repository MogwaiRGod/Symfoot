<?php

namespace App\Repository;

use App\Entity\Championnat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Championnat>
 *
 * @method Championnat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Championnat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Championnat[]    findAll()
 * @method Championnat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChampionnatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Championnat::class);
        $this->registry = $registry;
    }

    public function save(Championnat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Championnat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // // méthode vérifiant qu'une année entrée en argument n'a pas déjà un championnat d'enregistré
    // public static function checkYear($year) : bool {
    //     $repo = new ChampionnatRepository($this->registry);
    //     $allYears = [];

    //     // on récupère toutes les années où un championnat a eu lieu
    //     foreach($repo->findAll() as $champ) {
    //         array_push($allYears, $champ->getAnnee());
    //     }

    //     // si l'année est déjà enregistrée
    //     if(array_search($year, $allYears) !== FALSE) {
    //         return FALSE;
    //     }
    //     return TRUE;
    // }
//    /**
//     * @return Championnat[] Returns an array of Championnat objects
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

//    public function findOneBySomeField($value): ?Championnat
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

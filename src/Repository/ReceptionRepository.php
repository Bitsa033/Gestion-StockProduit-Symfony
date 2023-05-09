<?php

namespace App\Repository;

use App\Entity\Achat;
use App\Entity\Reception;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Reception|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reception|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reception[]    findAll()
 * @method Reception[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reception::class);
    }

    public function connection_to_databse(){

        try {
            $pdo=new \PDO('mysql:host=localhost;dbname=gps','root','');
        } catch (Exception $th) {
            die( $th->getMessage());
        }

        return $pdo;
    }

    public function sommeQte(Achat $commande)
    {
        $conn = $this->connection_to_databse();
        $sql = '
        SELECT SUM(qte) as qte FROM reception WHERE commande_id=:commande
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'commande'=>$commande->getId(),
        ]);

        $resultat=$stmt->fetchAll();

        // returns an array of arrays (i.e. a raw data set)
        return $resultat[0]['qte'];
        
    }

    // /**
    //  * @return Reception[] Returns an array of Reception objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Reception
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

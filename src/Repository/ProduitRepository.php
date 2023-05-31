<?php

namespace App\Repository;

use App\Entity\Produit;
use App\Service\Db\Db;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PDO;

/**
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public $db;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
        
        $this->db=new Db();
    }

    // /**
    //  * @return Produit[] Returns an array of Produit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    
    public function prod_qty_tot($id)
    {
        $sql1='SELECT produit_id, reception.qte_tot_val as qte_tot_val, reception.prix_tot_val
        AS prix_total_val FROM `reception` INNER JOIN achat ON achat.id = reception.commande_id 
        INNER JOIN produit ON produit.id = achat.produit_id WHERE reception.id=(select reception
        .id from reception ORDER by id DESC LIMIT 0,1 )';
        $array=$this->db->fetch_one_command($sql1);
        $produit_id = $array['produit_id'];
        $qte_tot_val = $array['qte_tot_val'];
        $prix_total_val = $array['prix_total_val'];

        $data = array(
            'produit_id'  => $produit_id,
            'qte_tot_val' => $qte_tot_val,
            'prix_total_val' =>$prix_total_val

        );

        return $data;
    }

    public function identity_prod($id)
    {
        $sql1='SELECT produit.id as id from produit
        INNER JOIN stock on stock.produit_id=produit.id
        WHERE stock.produit_id ='.$id;
        $array=$this->db->fetch_one_command($sql1);
        $produit_id =(is_bool($array) == true) ? $array :$array['id'];

        $data = array(
            'produit_id'  => $produit_id,
        );

        return $data;
    }

    public function nouveau_stock($id)
    {
        $bilanp=$this->prod_qty_tot($id);
        $afficherp=$this->identity_prod($id);
        $produit=$afficherp['produit_id'];
        $produit_id=$bilanp['produit_id'];
        $qte_tot_val=$bilanp['qte_tot_val'];
        $prix_total_val= $bilanp['prix_total_val'];

        if ($produit) {
            # code...
            //dd('meme id: ',$produit);
            $sql = " UPDATE `stock` SET `qte_tot`= qte_tot + $qte_tot_val,
            stock.prix_total = stock.prix_total + $prix_total_val WHERE produit_id =
            (SELECT DISTINCT produit.id from reception INNER join 
            achat ON achat.id = reception.commande_id 
            INNER JOIN produit on produit.id = achat.produit_id WHERE achat.id=".$id." )
            ";
            $this->db->insert_command($sql);
        } else {
            # code...
            //dd('dif id: ',$produit_id,$produit);
            $sql = " INSERT INTO `stock`(`produit_id`, `qte_tot`, `prix_total`) VALUES 
            ('$produit_id','$qte_tot_val','$prix_total_val') ";
                $this->db->insert_command($sql);
        }
        

    }

    
}

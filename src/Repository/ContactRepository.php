<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function getContacts($page, $nbPerPage){
        // Méthode qui récupère tous les messages de contact avec une pagination (utilisation de la classe Paginator)
        // La méthode prend deux arguments : la page actuelle $page et le nombre de messages par page $nbPerPage

        // Définition d'une requête personnalisée à l'aide de QueryBuilder
        $qb = $this->createQueryBuilder('c');
        $qb 
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery();

        $qb
            ->setFirstResult(($page-1)*$nbPerPage) // Premier résultat à afficher avec $nbPerPage = 4 (Quatre formations par page)
            ->setMaxResults($nbPerPage); // Quatre formations maximum par page

        // On retourne l'objet Paginator correspondant à la requête construite
        return new Paginator($qb, true);
    }


    public function getLastContacts($limit) {
        // Méthode qui récupère les messages de contact ($limit) publiés
        // Pour un affichage en back-office 
        
        // Création d'une requête personnalisée à l'aide du QueryBuilder
        return $this->createQueryBuilder('c')
        // Définition des critères de requête
            ->orderBy('c.createdAt', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }






    // /**
    //  * @return Contact[] Returns an array of Contact objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Contact
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

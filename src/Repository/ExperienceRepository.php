<?php

namespace App\Repository;

use App\Entity\Experience;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Experience|null find($id, $lockMode = null, $lockVersion = null)
 * @method Experience|null findOneBy(array $criteria, array $orderBy = null)
 * @method Experience[]    findAll()
 * @method Experience[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExperienceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Experience::class);
    }

    public function getExperiences($page, $nbPerPage){
        // Méthode qui récupère toutes les expériences avec une pagination (utilisation de la classe Paginator)
        // La méthode prend deux arguments : la page actuelle $page et le nombre d'expériences par page $nbPerPage

        // Définition d'une requête personnalisée à l'aide de QueryBuilder
        $qb = $this->createQueryBuilder('e');
        $qb 
            ->orderBy('e.periodStart', 'DESC')
            ->getQuery();

        $qb
            ->setFirstResult(($page-1)*$nbPerPage) // Premier résultat à afficher avec $nbPerPage = 3 (Trois expériences par page)
            ->setMaxResults($nbPerPage); // Trois expériences maximum par page

        // On retourne l'objet Paginator correspondant à la requête construite
        return new Paginator($qb, true);
    }


    public function getLastExperiences($limit) {
        // Méthode qui récupère les dernieres expériences ($limit) publiées 
        
        // Création d'une requête personnalisée à l'aide du QueryBuilder
        return $this->createQueryBuilder('e')
        // Définition des critères de requête
            ->where('e.published = 1')
            ->orderBy('e.createdAt', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }



    // /**
    //  * @return Experience[] Returns an array of Experience objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Experience
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\Education;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Education|null find($id, $lockMode = null, $lockVersion = null)
 * @method Education|null findOneBy(array $criteria, array $orderBy = null)
 * @method Education[]    findAll()
 * @method Education[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EducationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Education::class);
    }

    public function getEducations($page, $nbPerPage){
        // Méthode qui récupère toutes les formations avec une pagination (utilisation de la classe Paginator)
        // La méthode prend deux arguments : la page actuelle $page et le nombre de formations par page $nbPerPage

        // Définition d'une requête personnalisée à l'aide de QueryBuilder
        $qb = $this->createQueryBuilder('e');
        $qb 
            ->orderBy('e.periodStart', 'DESC')
            ->getQuery();

        $qb
            ->setFirstResult(($page-1)*$nbPerPage) // Premier résultat à afficher avec $nbPerPage = 3 (Trois formations par page)
            ->setMaxResults($nbPerPage); // Trois formations maximum par page

        // On retourne l'objet Paginator correspondant à la requête construite
        return new Paginator($qb, true);
    }


    public function getLastEducations($limit) {
        // Méthode qui récupère les dernieres formations ($limit) publiées
        // Pour un affichage en back-office 
        
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

    public function getFrontEducations ($limit) {
        // Méthode qui récupère les dernieres formations ($limit) publiées
        // Pour un affichage en front-office
        
        // Création d'une requête personnalisée à l'aide du QueryBuilder
        return $this->createQueryBuilder('e')
        // Définition des critères de requête
            ->where('e.published = 1')
            ->orderBy('e.periodStart', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

}

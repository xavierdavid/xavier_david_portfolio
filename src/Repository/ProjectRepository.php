<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function getProjects($page, $nbPerPage) {
        // Méthode qui récupère tous les projets avec une pagination (utilisation de la classe Paginator)
        // La méthode prend deux arguments : la page actuelle $page et le nombre de projets par page $nbPerPage
        
        // Construction d'une requête personnalisée à l'aide du QueryBuilder
        // On utilise la méthode createQueryBuilder de l'EntityManager avec l'alias 'p' (pour project)
        $qb = $this->createQueryBuilder('p');
        // On définit les critères de la requête, ainsi que les jointures nécessaires, le cas échéant
        $qb      
            ->orderBy('p.createdAt', 'DESC') // Tri des projets par date décroissante
            ->getQuery();


      // On utilise l'objet 'Paginator' de Doctrine pour mettre en page l'affichage des résultats
      $qb
        // On définit l'annonce à partir de laquelle commencer la liste
        // Le Paginator retourne une liste de $nbPerPage d'annonce qui s'utilise comme n'importe quel tableau
        // On définit quel doit être le premier résultat à afficher
        // Ici $nbPerPage est définit arbitrairement à 2 dans le contrôleur ProjectController ...
        
        // Premier résultat à afficher avec $nbPerPage = 2 (Deux projets par page)
        ->setFirstResult(($page-1) * $nbPerPage) // // ... Ex: $page = 1 (défaut), 1er résultat = 0 et si $page = 3, 1er résultat = 4
        // Ainsi que le nombre de projets à afficher sur une page
        ->setMaxResults($nbPerPage); // Ici, 2 projets par page

      // On retourne l'objet Paginator correspondant à la requête construite
      // On n'oublie pas le use correspondant en début de fichier
      return new Paginator($qb, true);
    }


    public function getLastProjects() {
        // Méthode qui récupère les derniers projets publiés 
        
        // Création d'un QueryBuilder
        return $this->createQueryBuilder('p')
        // Définition des critères de requête
            ->where('p.published = 1')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }







    // /**
    //  * @return Project[] Returns an array of Project objects
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

    /*
    public function findOneBySomeField($value): ?Project
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

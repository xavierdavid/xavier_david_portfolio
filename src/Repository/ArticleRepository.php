<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder; // Utilisation du QueryBuilder

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }


    public function getArticles($page, $nbPerPage) {
        // Méthode qui récupère tous les articles avec une pagination (utilisation de la classe Paginator)
        // La méthode prend deux arguments : la page actuelle $page et le nombre d'annonce par page $nbPerPage
        
        // Construction d'une requête personnalisée à l'aide du QueryBuilder
        // On utilise la méthode createQueryBuilder de l'EntityManager avec l'alias 'a' (pour article)
        $qb = $this->createQueryBuilder('a');
        // On définit les critères de la requête, ainsi que les jointures nécessaires, le cas échéant
        $qb      
            ->orderBy('a.createdAt', 'DESC') // Tri des articles par date décroissante
            ->getQuery();


      // On utilise l'objet 'Paginator' de Doctrine pour mettre en page l'affichage des résultats
      $qb
        // On définit l'annonce à partir de laquelle commencer la liste
        // Le Paginator retourne une liste de $nbPerPage d'annonce qui s'utilise comme n'importe quel tableau
        // On définit quel doit être le premier résultat à afficher
        // Ici $nbPerPage est définit arbitrairement à 2 dans le contrôleur ArticleController ...
        // ... Ex: $page = 1, 1er résultat = 0 - $page = 3, 1er résultat = 6
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre d'annonce à afficher sur une page
        ->setMaxResults($nbPerPage);

      // On retourne l'objet Paginator correspondant à la requête construite
      // On n'oublie pas le use correspondant en début de fichier
      return new Paginator($qb, true);
    }

    public function getArticleWithComment(){
        // Méthode qui récupère l'article associé à un commentaire 

        // Création d'un QueryBuilder 
        return $this->createQueryBuilder('a')
        // Définition des critères de recherche : on effectue une jointure entre la table  principale 
            ->leftJoin('a.comments', 'c')
            ->addSelect('c')
            ->getQuery()
            ->getResult();
        }



    public function getLastArticles($limit) {
        // Méthode qui récupère les '$limit' derniers articles publiés 
        
        // Création d'un QueryBuilder
        return $this->createQueryBuilder('a')
        // Définition des critères de requête
            ->where('a.published = 1')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }





    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

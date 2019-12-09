<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function allArticles(){
        // On sélectionne les données à l'aide du repository qui gère l'entité 'Article'
        $repository = $this->getDoctrine()->getRepository(Article::class);

        // On récupère tous les articles
        $articles = $repository->findAll();

        // Renvoie une réponse : afficher le template article/index.html.twig
        return $this->render('article/all_articles.html.twig', [ 
            'controller_name' => 'ArticleController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/article/{id}", name="article_view")
     */
    public function articleView($id){
        //On sélectionne les données avec le repository qui gère l'entité 'Article'
        $repository = $this->getDoctrine()->getRepository(Article::class);
        // On récupère l'article correspondant à l'identifiant 
        $article = $repository->find($id);

        return $this->render('article/article_view.html.twig', [
            'article' => $article
        ]);
    }

    
    
    public function lastArticles($limit){
        // On récupère le service EntityManager de l'ORM Doctrine
        $entityManager = $this->getDoctrine()->getManager();

        // On récupère la liste des dernières annonces publiées ($limit)
        $lastArticles = $entityManager->getRepository(Article::class)->findBy(
            array(), // Pas de critère
            array('createdAt' => 'desc'), // On trie par date décroissante
            $limit, // On sélectionne $limit annonces
            0 // A partir de la première annonce
        );

        return $this->render('article/index_articles.html.twig', array(
            'lastArticles'=> $lastArticles));
            // Intérêt : le contrôleur passe ici les variables nécessaires au template 'index_articles.html.twig'
    }
    

}

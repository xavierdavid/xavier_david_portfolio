<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'page_title' => "Accueil",
        ]);
    }


    /**
     * @Route("/home_article", name="home_article")
     */
    public function homeArticles(Request $request, ObjectManager $objectManager){
        // Méthode qui récupère et affiche les derniers articles publiés dans la section frontend

        // On fixe le nombre d'articles à afficher dans la section frontend 
        $limit = 3;

        // On sélectionne les données à l'aide du repository qui gère l'entité 'Article'
        $repository = $this->getDoctrine()->getRepository(Article::class);

        // On récupère les articles 
        $articles = $repository->getLastArticles($limit);
        
        // Envoi de la réponse au template pour l'affichage 
        return $this->render('home/home_article.html.twig', [
            'articles' => $articles
        ]);

    }




}

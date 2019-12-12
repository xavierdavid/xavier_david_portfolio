<?php

namespace App\Controller;

use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Article; // Permet d'utiliser la classe 'Article'
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; // Permet de jeter des exceptions et d'attraper les erreurs
use Symfony\Component\Routing\Annotation\Route; // Permet de définir les routes grâce aux annotations
use Symfony\Component\Form\Extension\Core\Type\TextType; // Permet d'utiliser le champ TextType de la classe Form
use Symfony\Component\Form\Extension\Core\Type\TextareaType; // Permet d'utiliser les champ TextareaType de la classe Form
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Permet d'utiliser les fonctionnalités du contrôleur Symfony


class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function allArticles(){
        // Méthode qui récupère tous les articles
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
     * @Route("/article/create", name="article_create")
     */
    public function createArticle(Request $request, ObjectManager $manager) {
        // Méthode qui créé un nouvel article - Injection de l'objet 'Request' et de l'objet 'ObjectManager'
        
        // On crée une instance 'vide' de la classe Article
        $article = new Article();

        // On créé un formulaire lié à notre instance 'article' à l'aide de notre classe de formulaire ArticleType
        $form = $this->createForm(ArticleType::class, $article);

        // On fait le lien Requête <-> Formulaire. La variable $article contient alors les valeurs entrées dans le formulaire
        $form->handleRequest($request);

        // On vérifie que le formulaire a été soumis à l'aide de la méthode isSubmitted de la classe Form 
        // On vérifie également qu'il est valide
        if($form->isSubmitted() && $form->isValid()){
            // On rajoute la date de création de l'article ...  
            $article->setCreatedAt(new \DateTime());

            // On demande au manager de persister l'entité 'article' : on l'enregistre pour qu'elle soit gérée par Doctrine 
            $manager->persist($article);
            // On demande au manager d'exécuter la requête ('INSERT INTO')
            $manager->flush();

            // On définit une message flash (variable de session qui ne dure que sur une seule page) ...
            // ... à l'aide de la méthode 'add' qui utilise en interne l'objet SESSION
            $request->getSession()->getFlashBag()->add('notice', 'Article bien enregistré');
            
            
            // Après avoir effectué la requête, on redirige vers la route 'article_view' avec en paramètre l'identifiant de l'article qui vient d'être créé
            return $this->redirectToRoute('article_view', [
                'id' => $article->getId()
            ]);

        }

        return $this->render('article/article_create.html.twig', [
            'formArticle' => $form->createView() // On transmet le résultat de la méthode créateView() de l'objet $form à la vue
        ]);
    }


    /**
     * @Route("/article/edit/{id}", name="article_edit")
     */
    public function editArticle($id, Request $request, ObjectManager $manager){
        // Méthode qui récupère et modifie un article
        // On sélectionne les données à l'aide du repository qui gère l'entité 'Article'
        $repository = $this->getDoctrine()->getRepository(Article::class);

        // On récupère tous les articles
        $article = $repository->find($id);

        // Si l'entité 'Article' est nulle (l'id $id de l'article n'existe pas) ...
        if (null === $article) {
            // Alors on lance une exception indiquant que l'annonce n'existe pas
            throw new NotFoundHttpException("L'article d'id ".$id." n'existe pas.");
        }

        // On créé un formulaire lié à notre instance 'article' à l'aide de notre classe de formulaire ArticleType
        $form = $this->createForm(ArticleType::class, $article);

        // On fait le lien Requête <-> Formulaire. La variable $article contient alors les valeurs entrées dans le formulaire
        $form->handleRequest($request);

        // On vérifie que le formulaire a été soumis à l'aide de la méthode isSubmitted de la classe Form 
        // On vérifie également qu'il est valide
        if($form->isSubmitted() && $form->isValid()){
            // On rajoute la date de création de l'article ...  
            $article->setUpdatedAt(new \DateTime());

            // On demande au manager de persister l'entité 'article' : on l'enregistre pour qu'elle soit gérée par Doctrine 
            $manager->persist($article);
            // On demande au manager d'exécuter la requête ('INSERT INTO')
            $manager->flush();

            // On définit une message flash (variable de session qui ne dure que sur une seule page) ...
            // ... à l'aide de la méthode 'add' qui utilise en interne l'objet SESSION
            $request->getSession()->getFlashBag()->add('notice', 'Article modifié');
            
            
            // Après avoir effectué la requête, on redirige vers la route 'article_view' avec en paramètre l'identifiant de l'article qui vient d'être créé
            return $this->redirectToRoute('article_view', [
                'id' => $article->getId()
            ]);
        }

        return $this->render('article/article_edit.html.twig', [
            'formArticle' => $form->createView(), // On transmet le résultat de la méthode créateView() de l'objet $form à la vue article_edit.html.twig
            'editMode' => true // On transmet la variable editMode à 'true' à la vue pour changer le texte du bouton submit du formulaire
            ]);
    }




    /**
     * @Route("/article/view/{id}", name="article_view")
     */
    public function articleView($id){

        //On sélectionne les données avec le repository qui gère l'entité 'Article'
        $repository = $this->getDoctrine()->getRepository(Article::class);
        // On récupère l'article correspondant à l'identifiant 
        $article = $repository->find($id);

        // Si l'entité 'Article' est nulle (l'id $id de l'article n'existe pas) ...
        if (null === $article) {
            // Alors on lance une exception indiquant que l'annonce n'existe pas
            throw new NotFoundHttpException("L'article d'id ".$id." n'existe pas.");
        }
        
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

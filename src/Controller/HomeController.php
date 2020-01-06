<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Project;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
        $limit = 2;

        // On sélectionne les données à l'aide du repository qui gère l'entité 'Article'
        $repository = $this->getDoctrine()->getRepository(Article::class);

        // On récupère les articles 
        $articles = $repository->getLastArticles($limit);
        
        // Envoi de la réponse au template pour l'affichage 
        return $this->render('home/home_article.html.twig', [
            'articles' => $articles
        ]);
    }


    /**
     * @Route("/home_article_view/{id}", name="home_article_view", requirements={"id"="\d+"})
     */
    public function homeArticleView($id, Request $request, ObjectManager $manager){
        // Méthode qui récupère et affiche l'article avec l'identifiant $id 
        
        // Sélection des données 
        $repository = $this->getDoctrine()->getRepository(Article::class);
        // Récupération de l'article 
        $article = $repository->find($id);

        // Si l'article $id n'existe pas... 
        if (null === $article) {
            throw new NotFoundHttpException("L'article d'id ".$id." n'existe pas.");
        }

        // Création et gestion du formulaire des commentaires 
         
        // On créé une instance de la classe 'Comment' 
        $comment = new Comment();
        // On créé un formulaire lié à notre instance 'comment' à l'aide de notre classe de formulaire CommentType
        $form = $this->createForm(CommentType::class, $comment);

        // On fait le lien Requête <-> Formulaire. La variable $comment contient alors les valeurs entrées dans le formulaire
        $form->handleRequest($request);

        // On vérifie que le formulaire a été soumis à l'aide de la méthode isSubmitted de la classe Form 
        // On vérifie également qu'il est valide
        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime()) // On rajoute la date de création du commentaire ...
                    ->setArticle($article); // On rattache le commentaire à l'article en cours

            // On demande au manager de persister l'entité 'comment' : on l'enregistre pour qu'elle soit gérée par Doctrine 
            $manager->persist($comment);

            // On demande au manager d'exécuter la requête ('INSERT INTO')
            $manager->flush();

            // On définit une message flash (variable de session qui ne dure que sur une seule page) ...
            // ... à l'aide de la méthode 'add' qui utilise en interne l'objet SESSION
            $request->getSession()->getFlashBag()->add('notice', 'Votre commentaire a bien été enregistré');

            // Après avoir effectué la requête, on redirige vers la route 'home_article_view' avec en paramètre l'identifiant de l'article en cours
            return $this->redirectToRoute('home_article_view', [
                'id' => $article->getId()
            ]);
        }
        
        return $this->render('home/home_article_view.html.twig', [
            'article' => $article,
            'formComment' => $form->createView() // On transmet le résultat de la méthode créateView() de l'objet $form à la vue
        ]);  
    }


    /**
     * @Route("/home_project",  name="home_project")
     */
    public function homeProjects(Request $request, ObjectManager $manager){
        // Méthode qui récupère et affiche les projets publiés 

        // Sélection des données à l'aide du repository 
        $repository = $this->getDoctrine()->getRepository(Project::class);
        // Récupération des projets 
        $projects = $repository->getLastProjects();

        // Réponse et affichage de la vue
        return $this->render('home/home_project.html.twig', [
            'projects' => $projects
        ]);
    }

}

<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\ArticleType;
use App\Service\FileUploader;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Article; // Permet d'utiliser la classe 'Article'
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route; // Permet de définir les routes grâce aux annotations
use Symfony\Component\Form\Extension\Core\Type\TextType; // Permet d'utiliser le champ TextType de la classe Form
use Symfony\Component\Form\Extension\Core\Type\TextareaType; // Permet d'utiliser les champ TextareaType de la classe Form
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; // Permet de jeter des exceptions et d'attraper les erreurs
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Permet d'utiliser les fonctionnalités du contrôleur Symfony


class ArticleController extends AbstractController
{
   
    /**
     * @Route("/article/{page}", name="article", requirements={"page"="\d+"}, defaults={"page" = 1})
     */
    public function allArticles($page){
        // Méthode qui récupère tous les articles et les affiche avec une pagination définie dans ArticleRepository 
        // $page est la page en cours. Elle doit obligatoirement être supérieure à 1
        if ($page<1){
            // Si $page <1, on déclenche une exception à l'aide de la méthode createNotFoundException de l'objet NotFoundHttpException
            // ... pour afficher une page d'erreur 404 (qui pourra ensuite être personnalisée)
            throw $this->createNotFoundException('Page "'.$page.'" inexistante.');
        }

        // On fixe arbitrairement le nombre d'annonces par page $nbPerPage, à 3
        // Mais bien sûr, il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
        $nbPerPage = 2;

        // On sélectionne les données à l'aide du repository qui gère l'entité 'Article'
        $repository = $this->getDoctrine()->getRepository(Article::class);

        // On récupère tous les articles
        $articles = $repository->getArticles($page, $nbPerPage);

        // On calcule le nombre total de pages à afficher ...
        // ... qui retourne le nombre total d'annonces count($listAdverts)
        // ... et détermine le nombre total de pages à afficher
        $nbPages = ceil(count($articles) / $nbPerPage);

        // Renvoie une réponse : afficher le template article/index.html.twig
        return $this->render('article/all_articles.html.twig', [ 
            'controller_name' => 'ArticleController',
            'articles' => $articles,
            'nbPages'=>$nbPages,
            'page' =>$page
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

            // On traite le fichier image téléchargé dans le formulaire dans le champ 'image'
            // On le récupère avec la méthode getData()
            $imageFile = $form['image']->getData();

            // Si un fichier image est présent (Rappel : le champ est facultatif)... 
            if($imageFile) {
                // On récupère le nom original du fichier
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // On laisse symfony affecter la bonne extension au fichier final
                $newFilename = $originalFilename.'.'. $imageFile->guessExtension();


                // On déplace le fichier vers le répertoire où seront stockées les images
                try {
                    $imageFile->move($this->getParameter('image_directory'),$newFilename);
                
                } catch (FileException $e) {
                    // ... On lance une exception dans le cas où le téléchargement connaîtrait une anomalie
                }

                // On met à jour la propriété imageFilename de l'entite Article pour stocker le nom du fichier dans la base de données
                // On utilse la méthode 'setImageFilename' de l'entité 'Article'
                $article->setImageFilename($newFilename);
            }
    
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
     * @Route("/article/edit/{id}", name="article_edit", requirements={"id"="\d+"})
     */
    public function editArticle($id, Request $request, ObjectManager $manager, FileUploader $fileUploader){
        // Méthode qui récupère et modifie un article
        // On sélectionne les données à l'aide du repository qui gère l'entité 'Article'
        // Injection du service FileUploader
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

            // On traite le fichier image téléchargé dans le formulaire dans le champ 'image'
            // On le récupère avec la méthode getData()
            $imageFile = $form['image']->getData();

            // Si un fichier image est présent (Rappel : le champ est facultatif)... 
            if($imageFile) {
                // On utilise le service FileUploader pour télécharger le fichier
                $newFilename = $fileUploader->upload($imageFile);              
                
                // On met à jour la propriété imageFilename de l'entite Article pour stocker le nom du fichier dans la base de données
                $article->setImageFilename($newFilename);
            }

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
            'formArticle' => $form->createView() // On transmet le résultat de la méthode créateView() de l'objet $form à la vue article_edit.html.twig
            //'editMode' => true // On transmet la variable editMode à 'true' à la vue pour changer le texte du bouton submit du formulaire
            ]);
    }



    /**
     * @Route("/article/view/{id}", name="article_view", requirements={"id"="\d+"})
     */
    public function articleView($id){
        // Méthode qui récupère et affiche un article

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



    /**
     * @Route("/article/delete/{id}", name="article_delete", requirements={"id"="\d+"})
     */
    public function articleDelete($id, Request $request, ObjectManager $manager){
        // Méthode qui récupère et supprime une article
        
        // On sélectionne les données
        $repository = $this->getDoctrine()->getRepository(Article::class);
        $article = $repository->find($id);

        if (null === $article) {
            throw new NotFoundHttpException(("L'article d'id " .$id." n'existe pas." ));
        }

        // On crée un formulaire vide, avec une protection contre les éventuelles attaques CSRF (Cross Site Request Forgery)     
        $form = $this->get('form.factory')->create();

        // On fait le lien Requête <-> Formulaire. La variable $article contient alors les valeurs 'vides' du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // On supprime l'article
            $manager->remove($article);
            $manager->flush();
            $request->getSession()->getFlashBag()->add('notice', "L'article a bien été supprimé.");
        
            // On redirige vers la route 'article'
            return $this->redirectToRoute('article');
        }

        // On appelle le template de suppression d'annonce
        return $this->render('article/article_delete.html.twig', array(
            'article' => $article,
            'form' => $form->createView(),
        ));
    }


    /**
     * @Route("/comment/delete/{id}", name="comment_delete", requirements={"id"="\d+"})
     */
    public function commentDelete($id, Request $request, ObjectManager $manager){
        // Méthode qui récupère et supprime un commentaire 

        // On sélectionne les données du commentaire
        $repository = $this->getDoctrine()->getRepository(Comment::class);
        $comment = $repository->find($id);

        // Si le commentaire n'existe pas ... 
        if(null === $comment) {
            throw new NotFoundHttpException(("Le commentaire d'id " .$id." n'existe pas." ));
        }

        // On crée un formulaire vide, avec une protection contre les éventuelles attaques CSRF (Cross Site Request Forgery)     
        $form = $this->get('form.factory')->create();

        // On fait le lien Requête <-> Formulaire. La variable $article contient alors les valeurs 'vides' du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // On supprime le commentaire
            $manager->remove($comment);
            $manager->flush();
            $request->getSession()->getFlashBag()->add('notice', "Le commentaire a bien été supprimé.");
        
            // On redirige vers la route 'article'
            return $this->redirectToRoute('article_view', [
                'id' => $comment->getArticle()->getId()
            ]);
        }

        // On appelle le template de suppression d'annonce
        return $this->render('article/comment_delete.html.twig', array(
            'comment' => $comment,
            'article' => $comment->getArticle($id), // On récupère l'article associé au commentaire
            'form' => $form->createView()
        ));




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

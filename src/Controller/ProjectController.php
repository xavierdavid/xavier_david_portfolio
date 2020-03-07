<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ArticleType;
use App\Form\ProjectType;
use App\Service\FileUploader;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
#use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Article; // Permet d'utiliser la classe 'Article'
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Annotation\Route; // Permet de définir les routes grâce aux annotations
use Symfony\Component\Form\Extension\Core\Type\TextType; // Permet d'utiliser le champ TextType de la classe Form
use Symfony\Component\Form\Extension\Core\Type\TextareaType; // Permet d'utiliser les champ TextareaType de la classe Form
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; // Permet de jeter des exceptions et d'attraper les erreurs
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Permet d'utiliser les fonctionnalités du contrôleur Symfony



class ProjectController extends AbstractController
{

    /**
     * @Route("/admin/project/{page}", name="project", requirements={"page"="\d+"}, defaults={"page" = 1})
     */
    public function allProjects($page){
        // Méthode qui récupère tous les projets et les affiche avec une pagination définie dans ProjectRepository
        // $page est la page en cours. Elle doit obligatoirement être supérieure à 1
        if ($page<1){
            // Si $page <1, on déclenche une exception à l'aide de la méthode createNotFoundException de l'objet NotFoundHttpException
            // ... pour afficher une page d'erreur 404 (qui pourra ensuite être personnalisée)
            throw $this->createNotFoundException('Page "'.$page.'" inexistante.');
        }

        // On fixe arbitrairement le nombre de projets par page $nbPerPage, à 2
        $nbPerPage = 2;

        // On sélectionne les données à l'aide du repository qui gère l'entité 'Project'
        $repository = $this->getDoctrine()->getRepository(Project::class);

        // On récupère tous les projets
        $projects = $repository->getProjects($page, $nbPerPage);

        // On calcule le nombre total de pages à afficher ...
        // ... qui retourne le nombre total de projets : count($listAdverts)
        // ... et détermine le nombre total de pages à afficher
        $nbPages = ceil(count($projects) / $nbPerPage); // Arrondi à l'entier supérieur avec 'ceil()'

        // Renvoie une réponse : afficher le template project/all_projects.html.twig
        return $this->render('project/all_projects.html.twig', [
            'controller_name' => 'ProjectController',
            'projects' => $projects,
            'nbPages'=>$nbPages,
            'page' =>$page
        ]);
    }



    /**
     * @Route("/admin/project/create", name="project_create")
     */
    public function createProject(Request $request, EntityManagerInterface $manager) {
        // Méthode qui créé un nouveau projet - Injection de l'objet 'Request' et de l'objet 'EntityManagerInterface'

        // On crée une instance 'vide' de la classe Project
        $project = new Project();

        // On créé un formulaire lié à notre instance 'project' à l'aide de notre classe de formulaire ProjectType
        $form = $this->createForm(ProjectType::class, $project);

        // On fait le lien Requête <-> Formulaire. La variable $project contient alors les valeurs entrées dans le formulaire
        $form->handleRequest($request);

        // On vérifie que le formulaire a été soumis à l'aide de la méthode isSubmitted de la classe Form
        // On vérifie également qu'il est valide
        if($form->isSubmitted() && $form->isValid()){
            // On rajoute la date de création du projet ...
            $project->setCreatedAt(new \DateTime());
            // On rajoute la date de mise à jour du projet ...
            $project->setUpdatedAt(new \DateTime());

            // On traite le fichier image téléchargé dans le formulaire dans le champ 'image'
            // On le récupère avec la méthode getData()
            $imageFile = $form['imageFilename']->getData();

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

                // On met à jour la propriété imageFilename de l'entite Project pour stocker le nom du fichier dans la base de données
                // On utilse la méthode 'setImageFilename' de l'entité 'Project'
                $project->setImageFilename($newFilename);
            }

            // On demande au manager de persister l'entité 'project' : on l'enregistre pour qu'elle soit gérée par Doctrine
            $manager->persist($project);

            // On demande au manager d'exécuter la requête ('INSERT INTO')
            $manager->flush();

            // On définit une message flash (variable de session qui ne dure que sur une seule page) ...
            // ... à l'aide de la méthode 'add' qui utilise en interne l'objet SESSION
            $request->getSession()->getFlashBag()->add('notice', 'Projet bien enregistré');

            // Après avoir effectué la requête, on redirige vers la route 'project_view' avec en paramètre l'identifiant de l'article qui vient d'être créé
            return $this->redirectToRoute('project_view', [
                'id' => $project->getId()
            ]);

        }

        return $this->render('project/project_create.html.twig', [
            'formProject' => $form->createView() // On transmet le résultat de la méthode créateView() de l'objet $form à la vue
        ]);
    }



    /**
     * @Route("/admin/project/edit/{id}", name="project_edit", requirements={"id"="\d+"})
     */
    public function editProject($id, Request $request, EntityManagerInterface $manager, FileUploader $fileUploader){
        // Méthode qui récupère et modifie un projet
        // On sélectionne les données à l'aide du repository qui gère l'entité 'Project'
        // Injection du service FileUploader
        $repository = $this->getDoctrine()->getRepository(Project::class);

        // On récupère le projet à éditer
        $project = $repository->find($id);

        // Si l'entité 'Project' est nulle (l'id $id du projet n'existe pas) ...
        if (null === $project) {
            // Alors on lance une exception indiquant que le projet n'existe pas
            throw new NotFoundHttpException("Le projet d'id ".$id." n'existe pas.");
        }

        // On créé un formulaire lié à notre instance 'project' à l'aide de notre classe de formulaire ProjectType
        $form = $this->createForm(ProjectType::class, $project);

        // On fait le lien Requête <-> Formulaire. La variable $project contient alors les valeurs entrées dans le formulaire
        $form->handleRequest($request);

        // On vérifie que le formulaire a été soumis à l'aide de la méthode isSubmitted de la classe Form
        // On vérifie également qu'il est valide
        if($form->isSubmitted() && $form->isValid()){
            // On rajoute la date de mise à jour du projet ...
            $project->setUpdatedAt(new \DateTime());

            // On traite le fichier image téléchargé dans le formulaire dans le champ 'image'
            // On le récupère avec la méthode getData()
            $imageFile = $form['imageFilename']->getData();

            // Si un fichier image est présent (Rappel : le champ est facultatif)...
            if($imageFile) {
                // On utilise le service FileUploader pour télécharger le fichier
                $newFilename = $fileUploader->upload($imageFile);

                // On met à jour la propriété imageFilename de l'entite Project pour stocker le nom du fichier dans la base de données
                $project->setImageFilename($newFilename);
            }

            // On demande au manager de persister l'entité 'project' : on l'enregistre pour qu'elle soit gérée par Doctrine
            $manager->persist($project);
            // On demande au manager d'exécuter la requête ('INSERT INTO')
            $manager->flush();

            // On définit une message flash (variable de session qui ne dure que sur une seule page) ...
            // ... à l'aide de la méthode 'add' qui utilise en interne l'objet SESSION
            $request->getSession()->getFlashBag()->add('notice', "Le projet a bien été modifié");

            // Après avoir effectué la requête, on redirige vers la route 'project_view' avec en paramètre l'identifiant du projet qui vient d'être modifié
            return $this->redirectToRoute('project_view', [
                'id' => $project->getId()
            ]);
        }

        return $this->render('project/project_edit.html.twig', [
            'formProject' => $form->createView(),
            'project' => $project // On transmet le résultat de la méthode créateView() de l'objet $form à la vue article_edit.html.twig
            ]);
    }



    /**
     * @Route("/admin/project/view/{id}", name="project_view", requirements={"id"="\d+"})
     */
    public function projectView($id){
        // Méthode qui récupère et affiche un projet

        //On sélectionne les données avec le repository qui gère l'entité 'Project'
        $repository = $this->getDoctrine()->getRepository(Project::class);
        // On récupère le projet correspondant à l'identifiant
        $project = $repository->find($id);

        // Si l'entité 'Project' est nulle (l'id $id de l'article n'existe pas) ...
        if (null === $project) {
            // Alors on lance une exception indiquant que le projet n'existe pas
            throw new NotFoundHttpException("Le projet d'id ".$id." n'existe pas.");
        }

        return $this->render('project/project_view.html.twig', [
            'project' => $project
        ]);
    }



    /**
     * @Route("/admin/project/delete/{id}", name="project_delete", requirements={"id"="\d+"})
     */
    public function projectDelete($id, Request $request, EntityManagerInterface $manager){
        // Méthode qui récupère et supprime un projet

        // On sélectionne les données
        $repository = $this->getDoctrine()->getRepository(Project::class);
        $project = $repository->find($id);

        if (null === $project) {
            throw new NotFoundHttpException(("Le projet d'id " .$id." n'existe pas." ));
        }

        // On crée un formulaire vide, avec une protection contre les éventuelles attaques CSRF (Cross Site Request Forgery)
        $form = $this->get('form.factory')->create();

        // On fait le lien Requête <-> Formulaire. La variable $project contient alors les valeurs 'vides' du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // On supprime le projet
            $manager->remove($project);
            $manager->flush();
            $request->getSession()->getFlashBag()->add('notice', "Le projet a bien été supprimé.");

            // On redirige vers la route 'project'
            return $this->redirectToRoute('project');
        }

        // On appelle le template de suppression de projet
        return $this->render('project/project_delete.html.twig', array(
            'project' => $project,
            'form' => $form->createView(),
        ));
    }




    public function lastProjects($limit){
        // On récupère le service EntityManager de l'ORM Doctrine
        $entityManager = $this->getDoctrine()->getManager();

        // On récupère la liste des dernières annonces publiées ($limit)
        $lastProjects = $entityManager->getRepository(Project::class)->findBy(
            //array(), // Pas de critère
            array('published' => '1'), // Projets publiés uniquement
            array('createdAt' => 'desc'), // On trie par date décroissante
            $limit, // On sélectionne $limit annonces
            0 // A partir de la première annonce
        );

        return $this->render('project/index_projects.html.twig', array(
            'lastProjects'=> $lastProjects));
            // Intérêt : le contrôleur passe ici les variables nécessaires au template 'index_projects.html.twig'
    }












}

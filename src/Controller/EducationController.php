<?php

namespace App\Controller;

use App\Entity\Education;
use App\Form\EducationType;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EducationController extends AbstractController
{
    /**
     * @Route("/admin/education/{page}", name="education", requirements={"page"="\d+"}, defaults={"page"= 1})
     */
    public function allEducations($page){
        // Méthode qui récupère toutes les formations et les affiche avec une pagination définie dans ExperienceRepository
        // $page est la page en cours (obligatoirement supérieure à 1)

        if($page <1) {
            throw $this->createNotFoundException('Page "'.$page .'" inexistante');
        }

        // Affichage de 3 formations par page
        $nbPerPage = 3;

        // Sélection des données à l'aide du repository
        $repository = $this->getDoctrine()->getRepository(Education::class);

        // Récupération de toutes les formations (méthode getEducations() de EducationRepository)
        $educations = $repository->getEducations($page, $nbPerPage);

        // Calcul du nombre total de pages à afficher (arrondi à l'entier supérieur)
        $nbPages = ceil(count($educations)/$nbPerPage);

        // Envoi d'une réponse au template all_educations.html.twig
        return $this->render('education/all_educations.html.twig', [
            'controller_name' => 'EducationController',
            'educations' => $educations,
            'nbPages' =>$nbPages,
            'page' => $page
        ]);
    }


    /**
     * @Route("/admin/education/create", name="education_create")
     */
    public function createEducation(Request $request, ObjectManager $manager, FileUploader $fileUploader) {
        // Méthode qui créé une nouvelle formation - Injection de dépendance : objet 'Request', objet 'ObjectManager' et service 'FileUploader'
    
        // On crée une instance 'vide' de la classe Education
        $education = new Education();

        // On créé un formulaire lié à notre instance 'education' à l'aide de notre classe de formulaire EducationType
        $form = $this->createForm(EducationType::class, $education);

        // On fait le lien Requête <-> Formulaire. La variable $education contient alors les valeurs entrées dans le formulaire
        $form->handleRequest($request);

        // On vérifie que le formulaire a été soumis à l'aide de la méthode isSubmitted de la classe Form 
        // On vérifie également qu'il est valide
        if($form->isSubmitted() && $form->isValid()){
            // On rajoute la date de création de la formation ...  
            $education->setCreatedAt(new \DateTime());

            // On traite le fichier image téléchargé dans le formulaire dans le champ 'image'
            // On le récupère avec la méthode getData()
            $imageFile = $form['imageFilename']->getData();

            // Si un fichier image est présent (Rappel : le champ est facultatif)... 
            if($imageFile) {
                // On utilise le service FileUploader pour télécharger le fichier
                $newFilename = $fileUploader->upload($imageFile);              
                // On met à jour la propriété imageFilename de l'entite Education pour stocker le nom du fichier dans la base de données
                $education->setImageFilename($newFilename);  
            }
    
            // On demande au manager de persister l'entité 'education' : on l'enregistre pour qu'elle soit gérée par Doctrine 
            $manager->persist($education);

            // On demande au manager d'exécuter la requête ('INSERT INTO')
            $manager->flush();

            // On définit une message flash (variable de session qui ne dure que sur une seule page) ...
            // ... à l'aide de la méthode getFlashBag()->'add()' qui utilise en interne l'objet SESSION
            $request->getSession()->getFlashBag()->add('notice', 'Formation bien enregistrée');
            
            // Après avoir effectué la requête, on redirige vers la route 'education_view' avec en paramètre l'identifiant de l'education qui vient d'être créée
            return $this->redirectToRoute('education_view', [
                'id' => $education->getId()
            ]);

        }

        return $this->render('education/education_create.html.twig', [
            'formEducation' => $form->createView() // On transmet le résultat de la méthode créateView() de l'objet $form à la vue
        ]);
    }


    /**
     * @Route("/admin/education/edit/{id}", name="education_edit", requirements={"id"="\d+"})
     */
    public function editEducation($id, Request $request, ObjectManager $manager, FileUploader $fileUploader){
        // Méthode qui récupère et modifie une formation
        // On sélectionne les données à l'aide du repository qui gère l'entité 'Education'
        // Injection du service FileUploader
        $repository = $this->getDoctrine()->getRepository(Education::class);

        // On récupère la formation à éditer
        $education = $repository->find($id);

        // Si l'entité 'Education' est nulle (l'id $id de la formation n'existe pas) ...
        if (null === $education) {
            // Alors on lance une exception indiquant que la formation n'existe pas
            throw new NotFoundHttpException("La formation d'id ".$id." n'existe pas.");
        }

        // On créé un formulaire lié à notre instance 'education' à l'aide de notre classe de formulaire ExperienceType
        $form = $this->createForm(EducationType::class, $education);

        // On fait le lien Requête <-> Formulaire. La variable $eeducation contient alors les valeurs entrées dans le formulaire
        $form->handleRequest($request);

        // On vérifie que le formulaire a été soumis à l'aide de la méthode isSubmitted de la classe Form 
        // On vérifie également qu'il est valide
        if($form->isSubmitted() && $form->isValid()){
            // On rajoute la date de mise à jour de la formation ...  
            $education->setUpdatedAt(new \DateTime());

            // On traite le fichier image téléchargé dans le formulaire dans le champ 'image'
            // On le récupère avec la méthode getData()
            $imageFile = $form['imageFilename']->getData();

            // Si un fichier image est présent (Rappel : le champ est facultatif)... 
            if($imageFile) {
                // On utilise le service FileUploader pour télécharger le fichier
                $newFilename = $fileUploader->upload($imageFile);              
                
                // On met à jour la propriété imageFilename de l'entite Education pour stocker le nom du fichier dans la base de données
                $education->setImageFilename($newFilename);
            }

            // On demande au manager de persister l'entité 'education' : on l'enregistre pour qu'elle soit gérée par Doctrine 
            $manager->persist($education);
            // On demande au manager d'exécuter la requête ('INSERT INTO')
            $manager->flush();

            // On définit une message flash (variable de session qui ne dure que sur une seule page) ...
            // ... à l'aide de la méthode getFlashBag()->add() qui utilise en interne l'objet SESSION
            $request->getSession()->getFlashBag()->add('notice', "La formation a bien été modifiée");
            
            // Après avoir effectué la requête, on redirige vers la route 'education_view' avec en paramètre l'identifiant de la formation qui vient d'être modifiée
            return $this->redirectToRoute('education_view', [
                'id' => $education->getId()
            ]);
        }

        return $this->render('education/education_edit.html.twig', [
            'formEducation' => $form->createView() // On transmet le résultat de la méthode créateView() de l'objet $form à la vue article_edit.html.twig
            ]);
    }


    /**
     * @Route("/admin/education/view/{id}", name="education_view", requirements={"id"="\d+"})
     */
    public function educationView($id){
        // Méthode qui récupère et affiche une expérience

        //On sélectionne les données avec le repository qui gère l'entité 'Education'
        $repository = $this->getDoctrine()->getRepository(Education::class);
        // On récupère la formation correspondant à l'identifiant 
        $education = $repository->find($id);

        // Si l'entité 'Education' est nulle (l'id $id de la formation n'existe pas) ...
        if (null === $education) {
            // Alors on lance une exception indiquant que la formation n'existe pas
            throw new NotFoundHttpException("La formation d'id ".$id." n'existe pas.");
        }
        
        return $this->render('education/education_view.html.twig', [
            'education' => $education
        ]);
    }

    
    /**
     * @Route("/admin/education/delete/{id}", name="education_delete", requirements={"id"="\d+"})
     */
    public function educationDelete($id, Request $request, ObjectManager $manager){
        // Méthode qui récupère et supprime une formation
        
        // On sélectionne les données
        $repository = $this->getDoctrine()->getRepository(Education::class);
        $education = $repository->find($id);

        if (null === $education) {
            throw new NotFoundHttpException(("La formation d'id " .$id." n'existe pas." ));
        }

        // On crée un formulaire vide, avec une protection contre les éventuelles attaques CSRF (Cross Site Request Forgery)     
        $form = $this->get('form.factory')->create();

        // On fait le lien Requête <-> Formulaire. La variable $education contient alors les valeurs 'vides' du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // On supprime la formation
            $manager->remove($education);
            $manager->flush();
            $request->getSession()->getFlashBag()->add('notice', "La formation a bien été supprimée.");
        
            // On redirige vers la route 'education'
            return $this->redirectToRoute('education');
        }

        // On appelle le template de suppression de la formation
        return $this->render('education/education_delete.html.twig', array(
            'education' => $education,
            'form' => $form->createView(),
        ));
    }


    public function lastEducations($limit){
        // Méthode qui récupère et affiche les '$limit' dernières formations publiées
        // On récupère le service EntityManager de l'ORM Doctrine
        $entityManager = $this->getDoctrine()->getManager();

        // On récupère la liste des dernières formations publiées ($limit) ... 
        // ... à l'aide de la requête personnalisée getLastEducations() de EducationRepository
        $lastEducations = $entityManager->getRepository(Education::class)->getLastEducations($limit);

        return $this->render('education/index_educations.html.twig', array(
            'lastEducations'=> $lastEducations));
            // Intérêt : le contrôleur passe ici les variables nécessaires au template 'index_educations.html.twig'
    }

    
    /**
     * Méthode qui permet de récupérer les données des formations professionnelles au format JSON
     * @Route("/get/educations", name="get_educations")
     */
    public function getEducations()
    {
        //On sélectionne les données avec le repository qui gère l'entité 'Experience'
        $repository = $this->getDoctrine()->getRepository(Education::class);
        // On récupère les données des expériences professionnelles au format JSON... 
        // ... pour les exploiter en Javascript avec Ajax 
        $educations = $repository->findAll();

        return $this->Json([
            'code'=> 200, 
            'message'=>'Tout fonctionne',
            'educations' => $educations
        ], 200);
    }
    
    
    
}

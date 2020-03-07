<?php

namespace App\Controller;

use App\Entity\Experience;
use App\Form\ExperienceType;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\Request;
#use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExperienceController extends AbstractController
{
    /**
     * @Route("/admin/experience/{page}", name="experience", requirements={"page"="\d+"}, defaults={"page"= 1})
     */
    public function allExperiences($page){
        // Méthode qui récupère toutes les expériences et les affiche avec une pagination définie dans ExperienceRepository
        // $page est la page en cours (obligatoirement supérieure à 1)

        if($page <1) {
            throw $this->createNotFoundException('Page "'.$page .'" inexistante');
        }

        // Affichage de 3 expériences par page
        $nbPerPage = 3;

        // Sélection des données à l'aide du repository
        $repository = $this->getDoctrine()->getRepository(Experience::class);

        // Récupération de toutes les expériences (méthode getExperiences() de ExperienceRepository)
        $experiences = $repository->getExperiences($page, $nbPerPage);

        // Calcul du nombre total de pages à afficher (arrondi à l'entier supérieur)
        $nbPages = ceil(count($experiences)/$nbPerPage);

        // Envoi d'une réponse au template all_experiences.html.twig
        return $this->render('experience/all_experiences.html.twig', [
            'controller_name' => 'ExperienceController',
            'experiences' => $experiences,
            'nbPages' =>$nbPages,
            'page' => $page
        ]);
    }


    /**
     * @Route("/admin/experience/create", name="experience_create")
     */
    public function createExperience(Request $request, EntityManagerInterface $manager, FileUploader $fileUploader) {
        // Méthode qui créé une nouvelle expérience - Injection de dépendance : objet 'Request', objet 'EntityManagerInterface' et service 'FileUploader'

        // On crée une instance 'vide' de la classe Experience
        $experience = new Experience();

        // On créé un formulaire lié à notre instance 'experience' à l'aide de notre classe de formulaire ExperienceType
        $form = $this->createForm(ExperienceType::class, $experience);

        // On fait le lien Requête <-> Formulaire. La variable $experience contient alors les valeurs entrées dans le formulaire
        $form->handleRequest($request);

        // On vérifie que le formulaire a été soumis à l'aide de la méthode isSubmitted de la classe Form
        // On vérifie également qu'il est valide
        if($form->isSubmitted() && $form->isValid()){
            // On rajoute la date de création de l'expérience ...
            $experience->setCreatedAt(new \DateTime());
            // On rajoute la date de mise à jour de l'expérience ...
            $experience->setUpdatedAt(new \DateTime());

            // On traite le fichier image téléchargé dans le formulaire dans le champ 'image'
            // On le récupère avec la méthode getData()
            $imageFile = $form['imageFilename']->getData();

            // Si un fichier image est présent (Rappel : le champ est facultatif)...
            if($imageFile) {

                // On utilise le service FileUploader pour télécharger le fichier
                $newFilename = $fileUploader->upload($imageFile);

                // On met à jour la propriété imageFilename de l'entite 'Experience' pour stocker le nom du fichier dans la base de données
                // On utilse la méthode 'setImageFilename' de l'entité 'Experience'
                $experience->setImageFilename($newFilename);
            }

            // On demande au manager de persister l'entité 'experience' : on l'enregistre pour qu'elle soit gérée par Doctrine
            $manager->persist($experience);

            // On demande au manager d'exécuter la requête ('INSERT INTO')
            $manager->flush();

            // On définit une message flash (variable de session qui ne dure que sur une seule page) ...
            // ... à l'aide de la méthode 'add' qui utilise en interne l'objet SESSION
            $request->getSession()->getFlashBag()->add('notice', 'Expérience bien enregistrée');

            // Après avoir effectué la requête, on redirige vers la route 'experience_view' avec en paramètre l'identifiant de l'expérience qui vient d'être créée
            return $this->redirectToRoute('experience_view', [
                'id' => $experience->getId()
            ]);

        }

        return $this->render('experience/experience_create.html.twig', [
            'formExperience' => $form->createView() // On transmet le résultat de la méthode créateView() de l'objet $form à la vue
        ]);
    }


    /**
     * @Route("/admin/experience/edit/{id}", name="experience_edit", requirements={"id"="\d+"})
     */
    public function editExperience($id, Request $request, EntityManagerInterface $manager, FileUploader $fileUploader){
        // Méthode qui récupère et modifie une expérience
        // On sélectionne les données à l'aide du repository qui gère l'entité 'Experience'
        // Injection du service FileUploader
        $repository = $this->getDoctrine()->getRepository(Experience::class);

        // On récupère l'expérience à éditer
        $experience = $repository->find($id);

        // Si l'entité 'Experience' est nulle (l'id $id de l'expérience n'existe pas) ...
        if (null === $experience) {
            // Alors on lance une exception indiquant que l'expérience n'existe pas
            throw new NotFoundHttpException("L'expérience d'id ".$id." n'existe pas.");
        }

        // On créé un formulaire lié à notre instance 'experience' à l'aide de notre classe de formulaire ExperienceType
        $form = $this->createForm(ExperienceType::class, $experience);

        // On fait le lien Requête <-> Formulaire. La variable $experience contient alors les valeurs entrées dans le formulaire
        $form->handleRequest($request);

        // On vérifie que le formulaire a été soumis à l'aide de la méthode isSubmitted de la classe Form
        // On vérifie également qu'il est valide
        if($form->isSubmitted() && $form->isValid()){
            // On rajoute la date de mise à jour de l'expérience ...
            $experience->setUpdatedAt(new \DateTime());

            // On traite le fichier image téléchargé dans le formulaire dans le champ 'image'
            // On le récupère avec la méthode getData()
            $imageFile = $form['imageFilename']->getData();

            // Si un fichier image est présent (Rappel : le champ est facultatif)...
            if($imageFile) {
                // On utilise le service FileUploader pour télécharger le fichier
                $newFilename = $fileUploader->upload($imageFile);

                // On met à jour la propriété imageFilename de l'entite Project pour stocker le nom du fichier dans la base de données
                $experience->setImageFilename($newFilename);
            }

            // On demande au manager de persister l'entité 'experience' : on l'enregistre pour qu'elle soit gérée par Doctrine
            $manager->persist($experience);
            // On demande au manager d'exécuter la requête ('INSERT INTO')
            $manager->flush();

            // On définit une message flash (variable de session qui ne dure que sur une seule page) ...
            // ... à l'aide de la méthode 'add' qui utilise en interne l'objet SESSION
            $request->getSession()->getFlashBag()->add('notice', "L'expérience a bien été modifiée");

            // Après avoir effectué la requête, on redirige vers la route 'experience_view' avec en paramètre l'identifiant de l'expérience qui vient d'être modifiée
            return $this->redirectToRoute('experience_view', [
                'id' => $experience->getId()
            ]);
        }

        return $this->render('experience/experience_edit.html.twig', [
            'formExperience' => $form->createView(),
            'experience' => $experience // On transmet le résultat de la méthode créateView() de l'objet $form à la vue article_edit.html.twig
            ]);
    }


    /**
     * @Route("/admin/experience/view/{id}", name="experience_view", requirements={"id"="\d+"})
     */
    public function experienceView($id){
        // Méthode qui récupère et affiche une expérience

        //On sélectionne les données avec le repository qui gère l'entité 'Experience'
        $repository = $this->getDoctrine()->getRepository(Experience::class);
        // On récupère l'expérience correspondant à l'identifiant
        $experience = $repository->find($id);

        // Si l'entité 'Experience' est nulle (l'id $id de l'expérience n'existe pas) ...
        if (null === $experience) {
            // Alors on lance une exception indiquant que l'expérience n'existe pas
            throw new NotFoundHttpException("L'expérience d'id ".$id." n'existe pas.");
        }

        return $this->render('experience/experience_view.html.twig', [
            'experience' => $experience
        ]);
    }


    /**
     * @Route("/admin/experience/delete/{id}", name="experience_delete", requirements={"id"="\d+"})
     */
    public function experienceDelete($id, Request $request, EntityManagerInterface $manager){
        // Méthode qui récupère et supprime une expérience

        // On sélectionne les données
        $repository = $this->getDoctrine()->getRepository(Experience::class);
        $experience = $repository->find($id);

        if (null === $experience) {
            throw new NotFoundHttpException(("L'expérience d'id " .$id." n'existe pas." ));
        }

        // On crée un formulaire vide, avec une protection contre les éventuelles attaques CSRF (Cross Site Request Forgery)
        $form = $this->get('form.factory')->create();

        // On fait le lien Requête <-> Formulaire. La variable $experience contient alors les valeurs 'vides' du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // On supprime l'expérience
            $manager->remove($experience);
            $manager->flush();
            $request->getSession()->getFlashBag()->add('notice', "L'expérience a bien été supprimée.");

            // On redirige vers la route 'experience'
            return $this->redirectToRoute('experience');
        }

        // On appelle le template de suppression d'expérience
        return $this->render('experience/experience_delete.html.twig', array(
            'experience' => $experience,
            'form' => $form->createView(),
        ));
    }


    public function lastExperiences($limit){
        // Méthode qui récupère et affiche les '$limit' dernières expériences publiées
        // On récupère le service EntityManager de l'ORM Doctrine
        $entityManager = $this->getDoctrine()->getManager();

        // On récupère la liste des dernières expériences publiées ($limit) ...
        // ... à l'aide de la requête personnalisée getLastExperiences() de ExperienceRepository
        $lastExperiences = $entityManager->getRepository(Experience::class)->getLastExperiences($limit);

        return $this->render('experience/index_experiences.html.twig', array(
            'lastExperiences'=> $lastExperiences));
            // Intérêt : le contrôleur passe ici les variables nécessaires au template 'index_experiences.html.twig'
    }

    /**
     * Méthode qui permet de récupérer les données des expériences professionnelles au format JSON
     * @Route("/get/experiences", name="get_experiences")
     */
    public function getExperiences()
    {
        //On sélectionne les données avec le repository qui gère l'entité 'Experience'
        $repository = $this->getDoctrine()->getRepository(Experience::class);
        // On récupère les données des expériences professionnelles au format JSON...
        // ... pour les exploiter en Javascript avec Ajax
        $experiences = $repository->findAll();

        return $this->Json([
            'code'=> 200,
            'message'=>'Tout fonctionne',
            'experiences' => $experiences
        ], 200);
    }




}

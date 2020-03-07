<?php

namespace App\Controller;

use App\Entity\Skill;
use App\Form\SkillType;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\Request;
#use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class SkillController extends AbstractController
{
    /**
     * @Route("/admin/skill/{page}", name="skill", requirements={"page"="\d+"}, defaults={"page" = 1})
     * Récupère toutes les compétences et les affiche avec une pagination définie dans SkillRepository
     */
    public function allSkills($page)
    {
        if($page <1){
            throw $this->createNotFoundException('Page "'.$page.'" inexistante');
        }

        // On fixe le nombre de compétence à afficher par page
        $nbPerPage = 5;

        // On récupère les données avec le repository qui gère l'entité Skill
        $repository = $this->getDoctrine()->getRepository(Skill::class);

        // On récupère toutes les compétences
        $skills = $repository->getSkills($page, $nbPerPage);

        // On calcule le nombre de page à afficher dans la vue
        $nbPages = ceil(count($skills) / $nbPerPage);

        // On renvoie une réponse à afficher au template
        return $this->render('skill/all_skills.html.twig', [
            'controller_name' => 'SkillController',
            'skills' => $skills, // On envoie les infos des compétences
            'nbPages' => $nbPages, // On envoie le nombre de pages à afficher
            'page' => $page // On envoie la page en cours
        ]);
    }



    /**
     * @Route("/admin/skill/create", name="skill_create")
     */
    public function createSkill(Request $request, EntityManagerInterface $manager) {
        // Méthode qui créé une nouvelle compétence - Injection de l'objet 'Request' et de l'objet 'EntityManagerInterface'

        // On crée une instance 'vide' de la classe Skill
        $skill = new Skill();

        // On créé un formulaire lié à notre instance 'skill' à l'aide de notre classe de formulaire SkillType
        $form = $this->createForm(SkillType::class, $skill);

        // On fait le lien Requête <-> Formulaire. La variable $skill contient alors les valeurs entrées dans le formulaire
        $form->handleRequest($request);

        // On vérifie que le formulaire a été soumis à l'aide de la méthode isSubmitted de la classe Form
        // On vérifie également qu'il est valide
        if($form->isSubmitted() && $form->isValid()){

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

                // On met à jour la propriété imageFilename de l'entite Skill pour stocker le nom du fichier dans la base de données
                // On utilse la méthode 'setImageFilename' de l'entité 'Skill'
                $skill->setImageFilename($newFilename);
            }

            // On demande au manager de persister l'entité 'skill' : on l'enregistre pour qu'elle soit gérée par Doctrine
            $manager->persist($skill);

            // On demande au manager d'exécuter la requête ('INSERT INTO')
            $manager->flush();

            // On définit une message flash (variable de session qui ne dure que sur une seule page) ...
            // ... à l'aide de la méthode 'add' qui utilise en interne l'objet SESSION
            $request->getSession()->getFlashBag()->add('notice', 'Compétence bien enregistrée');

            // Après avoir effectué la requête, on redirige vers la route 'skill_view' avec en paramètre l'identifiant de la compétence qui vient d'être créée
            return $this->redirectToRoute('skill_view', [
                'id' => $skill->getId()
            ]);

        }

        return $this->render('skill/skill_create.html.twig', [
            'formSkill' => $form->createView() // On transmet le résultat de la méthode créateView() de l'objet $form à la vue
        ]);
    }


    /**
     * @Route("/admin/skill/view/{id}", name="skill_view", requirements={"id"="\d+"})
     */
    public function skillView($id){
        // Méthode qui récupère et affiche une compétence

        // On sélectionne les données avec le repository qui gère l'entité 'Skill'
        $repository = $this->getDoctrine()->getRepository(Skill::class);
        // On récupère la compétence correspondante à l'identifiant
        $skill = $repository->find($id);

        // Si l'entité 'Skill' est nulle (l'id $id de la compétence n'existe pas) ...
        if (null === $skill) {
            // Alors on lance une exception indiquant que la compétence n'existe pas
            throw new NotFoundHttpException("La compétence d'id ".$id." n'existe pas.");
        }

        return $this->render('skill/skill_view.html.twig', [
            'skill' => $skill
        ]);
    }


    /**
     * @Route("/admin/skill/edit/{id}", name="skill_edit", requirements={"id"="\d+"})
     */
    public function editSkill($id, Request $request, EntityManagerInterface $manager, FileUploader $fileUploader){
        // Méthode qui récupère et modifie une compétence
        // Injection du service FileUploader

        // On sélectionne les données à l'aide du repository qui gère l'entité 'Skill'
        $repository = $this->getDoctrine()->getRepository(Skill::class);

        // On récupère la compétence à modifier
        $skill = $repository->find($id);

        // Si l'entité 'Skill' est nulle (l'id $id de la compétence n'existe pas) ...
        if (null === $skill) {
            // Alors on lance une exception indiquant que l'annonce n'existe pas
            throw new NotFoundHttpException("La compétence d'id ".$id." n'existe pas.");
        }

        // On créé un formulaire lié à notre instance 'skill' à l'aide de notre classe de formulaire SkillType
        $form = $this->createForm(SkillType::class, $skill);

        // On fait le lien Requête <-> Formulaire. La variable $skill contient alors les valeurs entrées dans le formulaire
        $form->handleRequest($request);

        // On vérifie que le formulaire a été soumis à l'aide de la méthode isSubmitted de la classe Form
        // On vérifie également qu'il est valide
        if($form->isSubmitted() && $form->isValid()){

            // On traite le fichier image téléchargé dans le formulaire dans le champ 'image'
            // On le récupère avec la méthode getData()
            $imageFile = $form['imageFilename']->getData();

            // Si un fichier image est présent (Rappel : le champ est facultatif)...
            if($imageFile) {
                // On utilise le service FileUploader pour télécharger le fichier
                $newFilename = $fileUploader->upload($imageFile);

                // On met à jour la propriété imageFilename de l'entite Skill pour stocker le nom du fichier dans la base de données
                $skill->setImageFilename($newFilename);
            }

            // On demande au manager de persister l'entité 'skill' : on l'enregistre pour qu'elle soit gérée par Doctrine
            $manager->persist($skill);
            // On demande au manager d'exécuter la requête ('INSERT INTO')
            $manager->flush();

            // On définit une message flash (variable de session qui ne dure que sur une seule page) ...
            // ... à l'aide de la méthode 'add' qui utilise en interne l'objet SESSION
            $request->getSession()->getFlashBag()->add('notice', "La compétence a bien été modifiée");

            // Après avoir effectué la requête, on redirige vers la route 'skill_view' avec en paramètre l'identifiant de la compétence qui vient d'être modifiée
            return $this->redirectToRoute('skill_view', [
                'id' => $skill->getId()
            ]);
        }

        return $this->render('skill/skill_edit.html.twig', [
            'formSkill' => $form->createView(),
            'skill' => $skill
            // On transmet le résultat de la méthode créateView() de l'objet $form à la vue skill_edit.html.twig
            //'editMode' => true // On transmet la variable editMode à 'true' à la vue pour changer le texte du bouton submit du formulaire
            ]);
    }


    /**
     * @Route("/admin/skill/delete/{id}", name="skill_delete", requirements={"id"="\d+"})
     */
    public function skillDelete($id, Request $request, EntityManagerInterface $manager){
        // Méthode qui récupère et supprime une compétence

        // On sélectionne les données
        $repository = $this->getDoctrine()->getRepository(Skill::class);
        $skill = $repository->find($id);

        if (null === $skill) {
            throw new NotFoundHttpException(("La compétence d'id " .$id." n'existe pas." ));
        }

        // On crée un formulaire vide, avec une protection contre les éventuelles attaques CSRF (Cross Site Request Forgery)
        $form = $this->get('form.factory')->create();

        // On fait le lien Requête <-> Formulaire. La variable $skill contient alors les valeurs 'vides' du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // On supprime La compétence
            $manager->remove($skill);
            $manager->flush();
            $request->getSession()->getFlashBag()->add('notice', "La compétence a bien été supprimée.");

            // On redirige vers la route 'skill'
            return $this->redirectToRoute('skill');
        }

        // On appelle le template de suppression de compétence
        return $this->render('skill/skill_delete.html.twig', array(
            'skill' => $skill,
            'form' => $form->createView(),
        ));
    }


    public function lastSkills($limit){
        // On récupère le service EntityManager de l'ORM Doctrine
        $entityManager = $this->getDoctrine()->getManager();

        // On récupère la liste des dernières compétences publiées ($limit)
        $lastSkills = $entityManager->getRepository(Skill::class)->findBy(
            array(), // Pas de critère
            array('name' => 'asc'), // On trie par nom et ordre croissant
            $limit, // On sélectionne $limit annonces
            /*0 // A partir de la première compétence*/
        );

        return $this->render('skill/index_skills.html.twig', array(
            'lastSkills'=> $lastSkills));
            // Intérêt : le contrôleur passe ici les variables nécessaires au template 'index_skills.html.twig'
    }
}

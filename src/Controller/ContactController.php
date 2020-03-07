<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\SendEmail;
use App\Repository\ContactRepository;
use App\Notification\ContactNotification;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
#use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * Méthode qui permet de gérer les informations saisies dans le formulaire de contact
     *  ... et d'envoyer une notification par email à l'administrateur
     * Injection de dépendances : Request, EntityManagerInterface et service SendEmail (configuré dans .env)
     * @Route("/contact", name="contact")
     */
    public function createContact(Request $request, EntityManagerInterface $manager, SendEmail $notification)
    {
        // Création d'un nouvel objet contact
        $contact = new Contact();

        // Création d'un formulaire de contact lié à notre instance $contact
        $form = $this->createForm(ContactType::class, $contact);

        // Liaison requête/formulaire : on permet au formulaire de 'traiter' la requête
        $form->handleRequest($request);

        // Vérification de la soumission et de la validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // On ajoute une date de création au nouveau contact
            $contact->setCreatedAt(new \DateTime());

            // On demande au manager de persister le nouveau contact
            $manager->persist($contact);

            // On demande au manager d'exécuter la requête ('INSERT INTO')
            $manager->flush();

            // On utilise le service SendEmail pour envoyer un email de notification à l'administrateur
            $notification->emailAdminNotification($contact);

            // Définition d'un message flash pour la confirmation de l'envoi des informations
            $request->getSession()->getFlashBag()->add('notice', 'Votre message a bien été envoyé');

            // Redirection après soumission du formulaire de contact : vers le formulaire de contact
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/contact.html.twig', [
            'controller_name' => 'ContactController',
            'formContact'=> $form->createView()
        ]);
    }


    /**
     * Méthode qui affiche tous les messages postés via le formulaire de contact
     * @Route("/admin/contact/{page}", name="all_contacts", requirements={"page"="\d+"}, defaults={"page" = 1})
     */
    public function allContacts($page, ContactRepository $repository){

        if($page<1){
            throw $this->createNotFoundException('Page "'.$page.'" inexistante.');
        }

        // On fixe à 4 le nombre de messages affiché sur chaque page
        $nbPerPage = 4;

        // Sélection des messages de contacts avec le repository qui gère l'entité Contact ...
        // ... on injecte ContactRepository pour l'utiliser directement (injection de dépendance)
        $repository = $this->getDoctrine()->getRepository(Contact::class);


        // Récupération de tous les messages de contact à l'aide de la méthode getContacts()
        $contacts = $repository->getContacts($page, $nbPerPage);

        // Calcul du nombre de pages à afficher dans la vue
        $nbPages = ceil(count($contacts) / $nbPerPage);

        // Envoi de la réponse à la vue
        return $this->render('contact/all_contacts.html.twig',[
            'controller_name' => 'ContactController',
            'contacts'=> $contacts,
            'nbPages'=> $nbPages,
            'page'=>$page,
        ]);

    }


    /**
     * Méthode qui récupère et supprime un contact
     * @Route("/admin/contact/delete/{id}", name="contact_delete", requirements={"id"="\d+"})
     */
    public function contactDelete($id, Request $request, EntityManagerInterface $manager, ContactRepository $repository ){

        // On sélectionne les données (injection de dépendance : ContactRepository)
        // On récupère le contact à partir de son id
        //$repository = $this->getDoctrine()->getRepository(Experience::class);
        $contact = $repository->find($id);

        // Si le contact n'existe pas, on lance une exception avec un message
        if (null === $contact) {
            throw new NotFoundHttpException(("Le contact d'id " .$id." n'existe pas." ));
        }

        // On crée un formulaire vide, avec une protection contre les éventuelles attaques CSRF (Cross Site Request Forgery)
        $form = $this->get('form.factory')->create();

        // On fait le lien Requête <-> Formulaire. La variable $contact contient alors les valeurs 'vides' du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // On supprime le contact
            $manager->remove($contact);
            $manager->flush();
            $request->getSession()->getFlashBag()->add('notice', "Le message a bien été supprimé.");

            // On redirige vers la route 'all_contacts'
            return $this->redirectToRoute('all_contacts');
        }

        // On appelle le template de suppression de contact
        return $this->render('contact/contact_delete.html.twig', array(
            'contact' => $contact,
            'form' => $form->createView(),
        ));
    }


    /**
     * Méthode qui récupère et affiche les '$limit' dernièrs messages de contact publiés
     */
    public function lastContacts($limit){

        // On récupère le service EntityManager de l'ORM Doctrine
        $entityManager = $this->getDoctrine()->getManager();

        // On récupère la liste des derniers messages de contacts ($limit) ...
        // ... à l'aide de la requête personnalisée getLastContacts() de ContactRepository
        $lastContacts = $entityManager->getRepository(Contact::class)->getLastContacts($limit);

        return $this->render('contact/index_contacts.html.twig', array(
            'lastContacts'=> $lastContacts));
            // Intérêt : le contrôleur passe ici les variables nécessaires au template 'index_contacts.html.twig'
    }

}

<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * Méthode qui permet de gérer les informations saisis dans le formulaire de contact
     * @Route("/contact", name="contact")
     */
    public function createContact(Request $request, ObjectManager $manager)
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
}

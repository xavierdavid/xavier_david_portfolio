<?php

namespace App\Service;

use App\Entity\User;
use Twig\Environment;
use App\Entity\Contact;


class SendEmail {

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $renderer;

    /**
     * Méthode qui récupère le service 'mailer' par injection de dépendance à l'aide d'un constructeur
     * Le constructeur est appelé par défaut et initialise la propriété $mailer et $renderer (rendu HTML/Twig)
     * $mailer est l'argument de '@mailer', identifiant du service (@) défini dans config/services.yaml
     *
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }


    /**
     * Méthode qui envoie une notification par email à l'administrateur à l'aide du service 'mailer', objet de gestion d'envoi de mail (composant Swiftmailer)
     * Le serveur d'envoi (SMTP) est paramétré dans le fichier '.env' (l'option qui annule l'envoi à partir du localhost est activée)
     */
    public function emailAdminNotification(Contact $contact)
    {
        /* Paramétrage du message */
        $message = (new \Swift_Message('Vous avez un nouveau message sur votre portfolio'))
            ->setFrom('noreply@xavier.com')
            ->setTo('xav.david28@gmail.com')
            ->setReplyTo($contact->getMail())
            ->setBody(
                $this->renderer->render(
                    // templates/emails/email_admin_notification.html.twig
                    'emails/email_admin_notification.html.twig', [
                        'contact' => $contact
                    ]),
                'text/html'
            )
        ;
        /* Envoi du message */
        $this->mailer->send($message);
    }


    /**
     * Méthode qui envoie une notification par email à un nouvel utilisateur après inscription 
     */
   public function emailUserNotification(User $user)
    {
        /* Paramétrage du message */
        $message = (new \Swift_Message('Confirmation de votre inscription sur le portfolio de Xavier DAVID'))
            ->setFrom('noreply@xavier-david.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderer->render(
                    // templates/email/emails/email_user_notification.html.twig 
                    'emails/email_user_notification.html.twig', [
                        'user'=> $user
                    ]),
                'text/html'
            )
        ;
        /* Envoi du message */
        $this->mailer->send($message);
    }


}
<?php

namespace App\Service;

use App\Entity\Contact;
use Twig\Environment;


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
     * Méthode qui envoie une notification par email à l'administrateur
     */
    public function emailNotification(Contact $contact)
    {
        $message = (new \Swift_Message('Vous avez un nouveau message sur votre portfolio'))
            ->setFrom('xav.david@gmail.com')
            ->setTo('xav.david28@gmail.com')
            ->setReplyTo($contact->getMail())
            ->setBody(
                $this->renderer->render(
                    // templates/emails/email_notification.html.twig
                    'emails/email_notification.html.twig', [
                        'contact' => $contact
                    ]),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }


}
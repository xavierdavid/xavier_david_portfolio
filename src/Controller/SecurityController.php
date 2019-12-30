<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder) {
        // Injection de dépendances : on a besoin de la requête HTTP pour analyser les informations saisies dans le formulaire ... 
        // ... de l'objectManager de Doctrine pour enregistrer l'utilisateur en base de données ... 
        // ... Ainsi que de UserPasswordEncoderInterface pour encoder le mot de passe de l'utilisateur
        
        // On créé un nouvel objet 'vide' de la classe 'User'
        $user = new User();
        
        // On créé un formulaire lié à notre instance 'user' à l'aide de notre classe de formulaire RegistrationType
        $form = $this->createForm(RegistrationType::class, $user);

        // On fait le lien entre la requête et le formulaire. La variable $user contient alors les valeurs saisies dans le formulaire
        $form->handleRequest($request);

        // On vérifie que le formulaire a été soumis à l'aide de la méthode isSubmitted de la classe Form 
        // On vérifie également qu'il est valide à l'aide de la méthode isValid
        if($form->isSubmitted() && $form->isValid()){

            // On procède au hachage du mot de passe de l'utilisateur avec l'algorithm 'bcrypt'
            // En apssant en paramètre $user, instance de notre classe Entité 'User' pour laquelle 'bcrypt' est paramétrée dans security.yaml
            // On récupère le mot de passe de l'utilisateur avec la méthode getPassword() de l'entité User
            $hash = $encoder->encodePassword($user, $user->getPassword());

            // On modifie le mot passe en le remplaçant par le mot de passe crypté 
            $user->setPassword($hash);

            // On attribue le rôle USER_USER par défaut à tous les utilisateurs 
            $user->setRoles(array('ROLE_USER'));
            
            // On demande au manager de persister l'entité 'user' : on l'enregistre pour qu'elle soit gérée par Doctrine 
            $manager->persist($user);

            // On demande au manager d'exécuter la requête ('INSERT INTO')
            $manager->flush();

            // Redirection vers la page de login
            return $this->redirectToRoute('security_login');

        }

        // On définit une message flash (variable de session qui ne dure que sur une seule page) ...
            // ... à l'aide de la méthode 'add' qui utilise en interne l'objet SESSION
            $request->getSession()->getFlashBag()->add('notice', 'Votre incription a bien été enregistrée.');


        // On appelle le template de rendu
        return $this->render('security/registration.html.twig', [
            'formRegistration' => $form->createView() // On transmet le résultat de la méthode créateView() de l'objet $form à la vue
        ]);

    }

    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils) {
        // Méthode qui gère la connexion de l'utilisateur
        // Injection de dépendance : on utilise la classe 'AuthenticationUtils'

        // Gestion des erreurs d'identification
        $error = $authenticationUtils->getLastAuthenticationError();

        // Dernier identifiant saisi par l'utilisateur 
        $lastUsername= $authenticationUtils->getLastUsername();

        // Renvoie vers la vue pour le traitement 
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }


    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(){
        // Méthode qui gère la déconnexion
    }

    
    
    
    
    
    /**
     * @Route("/security", name="security")
     */
    /*public function index()
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }*/
}

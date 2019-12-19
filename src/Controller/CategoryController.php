<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function allCategories(Request $request, ObjectManager $manager) {
        
        // On récupère et on affiche les catégories

        // On sélectionne les données à l'aide du repository qui gère l'entité 'Category'
        $repository = $this->getDoctrine()->getRepository(Category::class);
        // On récupère toutes les catégories
        //$categories = $repository->findAll();
        $categories = $repository->findMyCategories();

        // Création et gestion du formulaire d'ajout de catégories 
        // On crée une instance 'vide' de la classe 'Category'
        $category = new Category();

        // On créé un formulaire lié à notre instance 'category' à l'aide de notre classe de formulaire CategoryType
        $form = $this->createForm(CategoryType::class, $category);

        // On fait le lien Requête <-> Formulaire. La variable $category contient alors les valeurs entrées dans le formulaire
        $form->handleRequest($request);

        // On vérifie que le formulaire a été soumis à l'aide de la méthode isSubmitted de la classe Form 
        // On vérifie également qu'il est valide
        if($form->isSubmitted() && $form->isValid()){
            // On demande au manager de persister l'entité 'category' : on l'enregistre pour qu'elle soit gérée par Doctrine 
            $manager->persist($category);

            // On demande au manager d'exécuter la requête ('INSERT INTO')
            $manager->flush();

            // On définit une message flash (variable de session qui ne dure que sur une seule page) ...
            // ... à l'aide de la méthode 'add' qui utilise en interne l'objet SESSION
            $request->getSession()->getFlashBag()->add('notice', 'La catégorie a bien été ajoutée');
            
            // Après avoir effectué la requête, on redirige vers la route 'category' 
            return $this->redirectToRoute('category');
        }

        // On renvoie une réponse - Affichage des réponses par le template category/all_categories.html.twig
        return $this->render('category/all_categories.html.twig', [ 
            'categories' => $categories,
            'formCategory' => $form->createView() // On transmet le résultat de la méthode créateView() de l'objet $form à la vue
        ]);
        
        
    }




    /**
     * @Route("/category/delete/{id}", name="category_delete")
     */
    public function categoryDelete($id, Request $request, ObjectManager $manager){
        // Méthode qui récupère et supprime une catégorie 

        // On sélectionne les données de la catégorie  
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $category = $repository->find($id);

        // Si la catégorie n'existe pas ... 
        if(null === $category) {
            throw new NotFoundHttpException(("La catégorie d'id " .$id." n'existe pas." ));
        }

        // On crée un formulaire vide, avec une protection contre les éventuelles attaques CSRF (Cross Site Request Forgery)     
        $form = $this->get('form.factory')->create();

        // On fait le lien Requête <-> Formulaire. La variable $category contient alors les valeurs 'vides' du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // On supprime la catégorie
            $manager->remove($category);
            $manager->flush();
            $request->getSession()->getFlashBag()->add('notice', "La catégorie a bien été supprimée.");
        
            // On redirige vers la route 'category'
            return $this->redirectToRoute('category');
        }

        // On appelle le template de suppression de catégorie
        return $this->render('category/category_delete.html.twig', array(
            'category' => $category,
            'form' => $form->createView()
        ));

    }


}

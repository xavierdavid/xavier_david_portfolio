<?php

namespace App\DataFixtures;


use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Article;  
use Doctrine\Bundle\FixturesBundle\Fixture; 
use Doctrine\Common\Persistence\ObjectManager;


class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Utilisation de la librairie Faker - On instancie la classe Faker
        // Domaine de nom Faker, classe Factory avec la méthode create() qui prend en paramètre fr_FR (données en français)
        $faker = \Faker\Factory::create('fr_FR');
        
        // Création de 3 catégories Faker à l'aide d'une boucle
        for($i=1; $i <=3; $i++){
            $category = new Category();
            $category
                ->setTitle($faker->sentence()) // Utilisation de la methode sentence() pour générer des titres 
                ->setDescription($faker->paragraph()); // Utilisation de la methode sentence() pour générer des paragraphes
        
            $manager->persist($category);

            // Création entre 4 et 6 articles pour chaque catégorie 
            for($j=1; $j <= mt_rand(4, 6); $j++){ // Fonction PHP mt_rand permettant de générer un nombre au hasard entre 4 et 6
                // On instancie une classe Article
                $article = new Article();
                
                // Contenu des paragraphes des articles 
                // Un début de <p> auquel on joint 5 paragraphes (sous forme de tableau)... 
                // ... puis une fin de </p> et un début de <p>
                // ... puis une fin de </p> 
                $content = '<p>'.join($faker->paragraphs(5), '</p><p>').'</p>';
                
                $article
                    ->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setCreatedAt($faker->dateTimeBetween('-6 months')) // Utilisation de la méthode dateTimeBetween de Faker
                    ->setUpdatedAt(new \DateTime())
                    ->setCategory($category)
                    ->setPublished(true);
    
                $manager->persist($article); // On persiste (enregistre) l'article pour les préparer à être envoyés                
                
                // Création de commentaires pour l'article
                for($k=1; $k <= mt_rand(4, 10 ); $k++){

                    // On instancie une classe Comment
                    $comment = new Comment();         
                    
                    // Contenu des commentaires
                    $content = '<p>'.join($faker->paragraphs(2), '</p><p>').'</p>';
                    
                    // Contenu de la date de création des commentaires (doit être plus récente que la date de création des articles)
                    $days = (new \DateTime())->diff($article->getCreatedAt())->days; // Récupère la différence entre les deux objets DateTime
                                                            
                    $comment
                        ->setAuthor($faker->name)
                        ->setContent($content)
                        ->setCreatedAt($faker->dateTimeBetween('-' . $days. 'days'))
                        ->setArticle($article);
                    
                    $manager->persist($comment);
                    
                }
            
            }

        }  

        $manager->flush(); // On effectue la requête pour envoyer les données dans la base 
    }
}

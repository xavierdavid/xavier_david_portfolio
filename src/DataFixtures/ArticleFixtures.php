<?php

namespace App\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture; 
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;  


class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Création de 10 articles 
        for($i=1; $i <= 10; $i++){
            // On instancie une classe Article
            $article = new Article();
            $article
                ->setTitle("Titre de l'article n° $i")
                ->setContent("<p>Contenu de l'article n°$i</p>")
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime())
                ->setPublished(true);

            $manager->persist($article); // On persiste (enregistre) l'article pour les préparer à être envoyés
        }

        $manager->flush(); // On effectue la requête pour envoyer les données dans la base 
    }
}

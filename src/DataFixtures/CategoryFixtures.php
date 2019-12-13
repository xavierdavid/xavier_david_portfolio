<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // On créée un tableau de 5 catégoies
        $titles = array(
            'Développement frontend',
            'Développement backend',
            'Graphisme',
            'Intégration',
            'Réseau',
        );
        
        
        // On boucle le tableau $names pour récupérer chaque nom $name
        foreach ($titles as $title)
        {
            // On instancie l'objet 'Instance' Category
            $category = new Category();
            // On appelle la méthode setName pour envoyer chaque nom de catégorie dans la base
            $category->setTitle($title);
            // On persiste l'instance $category de l'entité Category
            $manager->persist($category);
        }
        // On déclenche l'enregistrement de toutes les catégories
        $manager->flush();
    }
}

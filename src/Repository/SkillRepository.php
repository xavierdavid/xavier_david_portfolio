<?php

namespace App\Repository;

use App\Entity\Skill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder; // Utilisation du QueryBuilder


/**
 * @method Skill|null find($id, $lockMode = null, $lockVersion = null)
 * @method Skill|null findOneBy(array $criteria, array $orderBy = null)
 * @method Skill[]    findAll()
 * @method Skill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Skill::class);
    }

    /**
      * Récupère toutes les compétences avec une pagination
     */
    public function getSkills($page, $nbPerPage){
        // La méthode prend deux arguments : la page actuelle $page et le nombre de compétences par page $nbPerPage
        
        // On construit une requête personnalisée avec le QueryBuilder
        $qb = $this->createQueryBuilder('s');
        // On définit les critères de la requête
        $qb
            ->orderBy('s.name', 'ASC') // Tri des compétences par nom et ordre croissant 
            ->getQuery();
        // On utilise l'objet Paginator de Doctrine pour la mise en page des résultats 
        $qb
            ->setFirstResult(($page-1) * $nbPerPage) // 1er résultat = 0
            ->setMaxResults($nbPerPage); // Nombre maximum de résultats par page

        // On retourne l'objet PAginator correspondant ) la requête construite 
        return new Paginator($qb, true);
        ;
    }


    public function getLastSkills($limit) {
        // Méthode qui récupère les '$limit' dernieres compétences publiées 
        
        // Création d'un QueryBuilder
        return $this->createQueryBuilder('s')
        // Définition des critères de requête
            ->orderBy('s.name', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    
}

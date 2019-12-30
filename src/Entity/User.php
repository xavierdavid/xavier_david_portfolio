<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert; // Permet d'utiliser le Validator pour soumettre des données à des contraintes pour valider les champs de formulaire (@Assert)

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 * fields={"email"},
 * message="L'email que vous avez saisi est déjà utilisé"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=8, minMessage="Votre mot de passe doit contenir 8 caractères au minimum")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Vos deux mots de passe doivent être identiques")
     */
    public $confirm_password;


    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    // Fonction complémentaire de l'interface UserInterface

    public function eraseCredentials(){}

    public function getSalt(){}

    public function getRoles():array {
        // Méthode qui renvoie une chaîne de caractères qui définit le rôle de l'utilisateur 
        
        $roles = $this->roles;
        // Garantir à tous les utilisateurs au minimum le rôle 'ROLE_USER'
        $roles[] = 'ROLE_USER';

        return array_unique($roles);        
        //return ['ROLE_USER'];
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

}

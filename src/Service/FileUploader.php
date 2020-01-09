<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory) {
    // Méthode avec constructeur permettant de définir le répertoire cible du téléchargement des fichiers
    // On définit à cet effet dans le constructeur, la propriété targetDirectory (répertoire cible)
    
        $this->targetDirectory = $targetDirectory;
    }

    
    public function upload(UploadedFile $file){
    // Méthode permettant de télécharger le fichier
       
    // On récupère le nom original du fichier
       $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
       // On laisse symfony affecter la bonne extension au fichier final
       $newFilename = $originalFilename.'.'. $file->guessExtension();


       // On déplace le fichier vers le répertoire où seront stockées les images
       try {
           $file->move($this->getTargetDirectory(),$newFilename);
       
       } catch (FileException $e) {
           // ... On lance une exception dans le cas où le téléchargement connaîtrait une anomalie
       } 

       return $newFilename;
    }

    
    
    public function getTargetDirectory(){
    // Méthode qui récupère le répertoire cible du téléchargement 
    return $this->targetDirectory;
    }

}

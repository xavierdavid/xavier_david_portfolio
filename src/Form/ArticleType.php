<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Construction du formulaire des articles
        $builder
            ->add('title', TextType::class,[
                'label' => 'Titre',
            ])
            ->add('category', EntityType::class,[
                'label' => 'Catégorie',
                'class' => Category::class,
                'choice_label' => 'title'
            ])
            ->add('introduction', TextareaType::class,[
                'label' => 'Introduction'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => ['class' => 'tinymce'],
                'required' => false
            ]) // Utilisation de l'éditeur TinyMCE sur le champ 'textarea'
             
            ->add('imageFilename', FileType::class,[
                'label' => 'Image',
                'mapped' => false, // Ce champs n'est pas associé à une propriété d'entité
                'required' => false, // Le fait de rendre ce champ facultatif permet d'éviter de re-télécharger l'image en cas d'édition
                //'constraints' => [ // Le champ n'étant pas rattaché à une entité, on ne peux pas définir de contraintes sous forme d'annotations dans l'entité, alors on les définit ici
                    //new File([
                        //'maxSize' => '1024k' // Taille maximum du fichier
                    //])
                //],
            ])
            ->add('published')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // Configuration permettant de rattacher le formulaire à l'entité Article
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}

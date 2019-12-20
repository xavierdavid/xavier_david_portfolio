<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class,[
                'label' => 'Titre',
            ])
            ->add('introduction', TextareaType::class,[
                'label' => 'Introduction',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => ['class' => 'tinymce'],
                'required' => false
            ]) // Utilisation de l'éditeur TinyMCE sur le champ 'textarea'
            ->add('period', TextType::class,[
                'label' => 'Période',
            ])
            ->add('url', UrlType::class,[
                'label' => 'Lien'
            ])
            ->add('image', FileType::class,[
                'label' => 'Image',
                'mapped' => false, // Ce champs n'est pas associé à une propriété d'entité
                'required' => false, // Le fait de rendre ce champ facultatif permet d'éviter de re-télécharger l'image en cas d'édition
            ])
            ->add('published')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}

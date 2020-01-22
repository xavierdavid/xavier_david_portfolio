<?php

namespace App\Form;

use App\Entity\Experience;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ExperienceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mission', TextType::class,[
                'label' => 'Poste',
            ])
            ->add('company', TextType::class,[
                'label' => 'Entreprise',
            ])
            ->add('address', TextType::class,[
                'label' => 'Adresse',
            ])
            ->add('periodStart', DateType::class,[
                'label' => 'Début',
                'widget' => 'single_text',
            ])
            ->add('periodEnd', DateType::class,[
                'label' => 'Fin',
                'widget' => 'single_text',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Missions',
                'attr' => ['class' => 'tinymce'],
                'required' => false
            ]) // Utilisation de l'éditeur TinyMCE sur le champ 'textarea'
            ->add('url', UrlType::class,[
                'label' => 'Lien',
                'required' => false,
            ])
            ->add('imageFilename', FileType::class,[
                'label' => 'Image',
                'mapped' => false, // Ce champs n'est pas associé à une propriété d'entité
                'required' => false, // Le fait de rendre ce champ facultatif permet d'éviter de re-télécharger l'image en cas d'édition
            ])
            ->add('published', CheckboxType::class, [
                'label' => 'Publier',
                'required' => false,
            ])
            ->add('ongoing', CheckboxType::class, [
                'label' => 'En cours',
                'required' => false,
            ])  
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Experience::class,
        ]);
    }
}

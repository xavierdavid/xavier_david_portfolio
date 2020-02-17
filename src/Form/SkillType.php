<?php

namespace App\Form;

use App\Entity\Skill;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SkillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Construction du formulaire de création des compétences
        $builder
            ->add('name', TextType::class,[
                'label' => 'Nom',
            ])
            ->add('category', EntityType::class,[
                'label' => 'Catégorie',
                'class' => Category::class,
                'choice_label' => 'title'
            ])
            ->add('level', TextType::class,[
                'label'=> 'Niveau',
            ])
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
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Skill::class,
        ]);
    }
}

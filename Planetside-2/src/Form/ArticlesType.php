<?php

namespace App\Form;

use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use App\Repository\TagsRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class ArticlesType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Ajoutez un titre",
            ])
            ->add('text', CKEditorType::class, array(
                'config' => array(
                    'toolbar' => 'full',
                    'uiColor' => '#52b8df',
                    'required' => true,
                    'language' => 'fr',
                ),
            ))
            ->add('tag', EntityType::class, [
                'class' => 'App:Tags',
                'choice_label' => 'nom',
                'label'     => 'Quels tags souhaitez vous ajouter ?',
                'expanded'  => false,
                'multiple'  => false,
                'required' => false,
            ])
            ->add('sub_tag', EntityType::class, [
                'class' => 'App:SubTags',
                'choice_label' => 'nom',
                'label'     => 'Quels sous-tags souhaitez vous ajouter ? (Pour les armes, véhicules et équipements)',
                'expanded'  => false,
                'multiple'  => false,
                'required' => false,
                'group_by' => 'sous_categorie',
            ])
            ->add('image', FileType::class,  [
                'label' => 'Ajouter une image', 
                'required' => false, 
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' =>'2000k', 
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'application/pdf'
                        ],
                        'mimeTypesMessage' => 'Le fichier doit être au format png,jpeg ou pdf',
                    ])
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Valider',
                'row_attr' => [
                    'class' => 'text-right'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}

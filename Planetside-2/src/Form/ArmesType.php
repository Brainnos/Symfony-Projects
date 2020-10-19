<?php

namespace App\Form;

use App\Entity\Armes;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArmesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('item_id')
            ->add('item_category_id')
            ->add('description')
            ->add('image_id')
            ->add('image_path')
            ->add('name_fr')
            ->add('faction_id')
            ->add('text', CKEditorType::class, array(
                'config' => array(
                    'toolbar' => 'full',
                    'uiColor' => '#52b8df',
                    'required' => true,
                    'language' => 'fr',
                ),
            ))
            ->add('sub_tag_arme', EntityType::class, [
                'class' => 'App:SubTags',
                'choice_label' => 'nom',
                'label'     => 'Quels sous-tags souhaitez vous ajouter ? (Pour les armes, véhicules et équipements)',
                'expanded'  => false,
                'multiple'  => false,
                'required' => false,
                'group_by' => 'sous_categorie',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Armes::class,
        ]);
    }
}

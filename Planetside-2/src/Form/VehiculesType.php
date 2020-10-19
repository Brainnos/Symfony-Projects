<?php

namespace App\Form;

use App\Entity\Vehicules;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehiculesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('name_fr')
            ->add('vehicle_id')
            ->add('description')
            ->add('image_path')
            ->add('text', CKEditorType::class, array(
                'config' => array(
                    'toolbar' => 'full',
                    'uiColor' => '#52b8df',
                    'required' => true,
                    'language' => 'fr',
                ),
            ))            
            ->add('sub_tag', EntityType::class, [
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
            'data_class' => Vehicules::class,
        ]);
    }
}

<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaisirFicheFraisForfaitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ForfaitEtape', IntegerType::class,  [
                'attr' => ['class' => 'tinymce'],
                'attr' => ['min' => '0']
            ])
            ->add('FraisKilometrique', IntegerType::class,  [
                'attr' => ['class' => 'tinymce'],
                'attr' => ['min' => '0']
            ])
            ->add('NuiteeHotel', IntegerType::class,  [
                'attr' => ['class' => 'tinymce'],
                'attr' => ['min' => '0']
            ])
            ->add('RepasRestaurant', IntegerType::class,  [
                'attr' => ['class' => 'tinymce'],
                'attr' => ['min' => '0']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

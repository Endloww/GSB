<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Comptable' => 'ROLE_COMPTABLE',
                    'Visiteur' => 'ROLE_VISITEUR',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('password')
            ->add('nom')
            ->add('prenom')
            ->add('adresse')
            ->add('cp')
            ->add('ville')
            ->add('dateEmbauche')
            ->add('old_id')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

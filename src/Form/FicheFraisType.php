<?php

namespace App\Form;

use App\Entity\FicheFrais;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FicheFraisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mois')
            ->add('nbJustificatifs', NumberType::class)
            ->add('montantValid')
            ->add('dateModif')
            ->add('user', EntityType::class, [
                'label' => 'utilisateur',
                'class' => User::class,
                'choice_label' => 'email',
            ])
            //->add('etat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FicheFrais::class,
        ]);
    }
}

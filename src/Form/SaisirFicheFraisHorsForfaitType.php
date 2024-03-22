<?php

namespace App\Form;

use App\Entity\LigneFraisHorsForfait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaisirFicheFraisHorsForfaitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'attr' => ['class' => 'inputMargin']
            ])
            ->add('date', DateType::class,[
                'widget' => 'choice',
                'format' => 'yyyyMMdd',
                'years' => [date('Y')],
                'months' => [date('m')]
            ])
            ->add('montant', MoneyType::class, [
                'currency' => 'EUR',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LigneFraisHorsForfait::class,
        ]);
    }
}

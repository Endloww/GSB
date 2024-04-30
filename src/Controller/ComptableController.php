<?php

namespace App\Controller;


use App\Entity\FicheFrais;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class ComptableController extends AbstractController
{
    #[Route('/comptable', name: 'app_comptable')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $form_visiteur = $this->createFormBuilder()
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName',
                'label' => false,
                'attr' => ['class' => 'form-select'],
            ])
            ->getForm();

    /*    $repository = $doctrine->getManager()->getRepository(FicheFrais::class);
        $query = $repository->createQueryBuilder('f')
            ->distinct('f.mois as month')
            ->getQuery();

        $months = $query->getResult();

        $monthChoices = [];
        foreach ($months as $month) {
            $monthChoices[$month['month']] = $month['month'];
        }

        $form_mois = $this->createFormBuilder()
            ->add('month', ChoiceType::class, [
                'choices' => $monthChoices,
                'label' => false,
                'attr' => ['class' => 'form-select'],
            ])
            ->getForm();
    */
        if ($form_visiteur->isSubmitted() && $form_visiteur->isValid()){
            $allFicheByUser = $doctrine->getRepository(FicheFrais::class)->findBy(['user' => $form_visiteur->get('user')->getData()]);

        } else {
            dd($form_visiteur);
            $allFicheByUser = [];
        }


        return $this->render('comptable/index.html.twig', [
            'form_visiteur' => $form_visiteur->createView(),
            'allFicheByUser' => $allFicheByUser,
           // 'form_mois' => $form_mois->createView(),
        ]);
    }
}

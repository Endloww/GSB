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

        $repository = $this->getDoctrine()->getRepository(FicheFrais::class);
        $query = $repository->createQueryBuilder('f')
            ->select('MONTH(f.mois) as month')
            ->groupBy('month')
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


        return $this->render('comptable/index.html.twig', [
            'form_visiteur' => $form_visiteur->createView(),
            'form_mois' => $form_mois->createView(),
        ]);
    }}

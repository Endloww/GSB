<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\LigneFraisHorsForfait;
use App\Form\EtatType;
use App\Form\SaisirFicheFraisForfaitType;
use App\Form\SaisirFicheFraisHorsForfaitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SaisirFicheFraisController extends AbstractController
{
    #[Route('/saisir/fiche/frais', name: 'app_saisir_fiche_frais')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $formFraisF = $this->createForm(SaisirFicheFraisForfaitType::class);
        $formFraisF->handleRequest($request);

        if ($formFraisF->isSubmitted() && $formFraisF->isValid()) {
            $entityManager->persist();
            $entityManager->flush();

            return $this->redirectToRoute('app_etat_index', [], Response::HTTP_SEE_OTHER);
        }

        $ligneFraisHorsForfait = new LigneFraisHorsForfait();
        $formFraisHF = $this->createForm(SaisirFicheFraisHorsForfaitType::class, $ligneFraisHorsForfait);
        $formFraisHF->handleRequest($request);

        if ($formFraisHF->isSubmitted() && $formFraisHF->isValid()) {
            $entityManager->persist($ligneFraisHorsForfait);
            $entityManager->flush();

            return $this->redirectToRoute('app_etat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('saisir_fiche_frais/index.html.twig', [
            'controller_name' => 'SaisirFicheFraisController',
            'formFraisHF' => $formFraisHF,
            'formFraisF' =>$formFraisF
        ]);
    }
}

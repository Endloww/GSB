<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\FicheType;

class FicheController extends AbstractController
{
    #[Route('/fiche', name: 'app_fiche')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $selectedFiche = null;
        $user = $this->getUser();
        $fichesFrais = $doctrine->getRepository(FicheFrais::class)->findBy(['user' => $user]);
        $form = $this->createForm(FicheType::class, $fichesFrais);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var FicheFrais $selectedFiche */
            $selectedFiche = $form->get('date')->getData();

        }

        // Rendez votre modèle Twig en passant le formulaire à votre vue
        return $this->render('fiche/index.html.twig', [
            'controller_name' => 'FicheController',
            'form' => $form->createView(), // Assurez-vous de passer la vue du formulaire
            'selectedFiche' => $selectedFiche,
        ]);
    }
}

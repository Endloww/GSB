<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InflationPrimeController extends AbstractController
{
    #[Route('/inflation/prime', name: 'app_inflation_prime')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $fichesFrais = $doctrine->getRepository(FicheFrais::class);

        $montantsValides2023 = $fichesFrais->getMontant(2023);

        $primeTotale = array_sum($montantsValides2023) * 0.095;

        return $this->render('inflation_prime/index.html.twig', [
            'primeTotale' => $primeTotale,

        ]);
    }
}


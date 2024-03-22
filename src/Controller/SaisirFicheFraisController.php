<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Form\EtatType;
use App\Form\FicheType;
use App\Form\SaisirFicheFraisForfaitType;
use App\Form\SaisirFicheFraisHorsForfaitType;
use App\Repository\EtatRepository;
use App\Repository\FicheFraisRepository;
use App\Repository\FraisForfaitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Stmt\If_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SaisirFicheFraisController extends AbstractController
{
    #[Route('/saisir-fiche-frais', name: 'app_saisir_fiche_frais')]
    public function index(Request $request, EntityManagerInterface $entityManager, FicheFraisRepository $ficheFraisRepository, FraisForfaitRepository $forfaitRepository, EtatRepository $etatRepository, ManagerRegistry $doctrine): Response
    {
        $dateActuel = new \DateTime('now');
        $moisActuel = $dateActuel->format('Ym');
        $ficheMoisActuel = $ficheFraisRepository->findOneBy(['user' => $this->getUser(), 'mois' => $moisActuel]);
        if ($ficheMoisActuel === null) {
            $ficheMoisActuel = new ficheFrais();
            $ligneFraisForfaitKilometre = new LigneFraisForfait();
            $ligneFraisForfaitEtape = new LigneFraisForfait();
            $ligneFraisForfaitNuitee = new LigneFraisForfait();
            $ligneFraisForfaitRepas = new LigneFraisForfait();
            $ligneFraisForfaitKilometre->setFicheFrais($ficheMoisActuel);
            $ligneFraisForfaitEtape->setFicheFrais($ficheMoisActuel);
            $ligneFraisForfaitNuitee->setFicheFrais($ficheMoisActuel);
            $ligneFraisForfaitRepas->setFicheFrais($ficheMoisActuel);
            $ligneFraisForfaitKilometre->setQuantite(0);
            $ligneFraisForfaitEtape->setQuantite(0);
            $ligneFraisForfaitNuitee->setQuantite(0);
            $ligneFraisForfaitRepas->setQuantite(0);
            $ligneFraisForfaitKilometre->setFraisForfait($forfaitRepository->find(2));
            $ligneFraisForfaitEtape->setFraisForfait($forfaitRepository->find(1));
            $ligneFraisForfaitNuitee->setFraisForfait($forfaitRepository->find(3));
            $ligneFraisForfaitRepas->setFraisForfait($forfaitRepository->find(4));
            $ficheMoisActuel->setMois($moisActuel);
            $ficheMoisActuel->setEtat($etatRepository ->find(2));
            $ficheMoisActuel->setUser($this -> getUser());
            $ficheMoisActuel->setNbJustificatifs(0);
            $ficheMoisActuel->setMontantValid(0);
            $ficheMoisActuel->setDateModif($dateActuel);
            $entityManager->persist($ficheMoisActuel);
            $entityManager->persist($ligneFraisForfaitKilometre);
            $entityManager->persist($ligneFraisForfaitEtape);
            $entityManager->persist($ligneFraisForfaitNuitee);
            $entityManager->persist($ligneFraisForfaitRepas);
            $entityManager->flush();
        }
        //Verification fiche du mois existante ou non
        $formFraisF = $this->createForm(SaisirFicheFraisForfaitType::class);
        $formFraisF->handleRequest($request);

        if ($formFraisF->isSubmitted() && $formFraisF->isValid()) {
            $toutesLesLignes = $ficheMoisActuel->getLigneFraisForfait();
            foreach ($toutesLesLignes as $lignes){
                if ($lignes->getFraisForfait()->getId() == 1){
                    $lignes->setQuantite($formFraisF->get('ForfaitEtape')->getData());
                }
                elseif ($lignes->getFraisForfait()->getId() == 2){
                    $lignes->setQuantite($formFraisF->get('FraisKilometrique')->getData());
                }
                elseif ($lignes->getFraisForfait()->getId() == 3){
                    $lignes->setQuantite($formFraisF->get('NuiteeHotel')->getData());
                }
                else {
                    $lignes->setQuantite($formFraisF->get('RepasRestaurant')->getData());
                }
            }

            $entityManager->persist($ficheMoisActuel);
            $entityManager->flush();

            return $this->redirectToRoute('app_etat_index', [], Response::HTTP_SEE_OTHER);
        }

        $ficheMoisUser = $ficheFraisRepository->findOneBy(['user' => $this->getUser(), 'mois' => $moisActuel]);
        if ($ficheMoisUser !== null) {
            foreach ($ficheMoisUser->getLigneFraisForfait() as $ligneFraisForfait) {
                if ($ligneFraisForfait->getFraisForfait()->getId() == 1) {
                    $formFraisF->get('ForfaitEtape')->setData($ligneFraisForfait->getQuantite());
                } elseif ($ligneFraisForfait->getFraisForfait()->getId() == 2) {
                    $formFraisF->get('FraisKilometrique')->setData($ligneFraisForfait->getQuantite());
                } elseif ($ligneFraisForfait->getFraisForfait()->getId() == 3) {
                    $formFraisF->get('NuiteeHotel')->setData($ligneFraisForfait->getQuantite());
                } else {
                    $formFraisF->get('RepasRestaurant')->setData($ligneFraisForfait->getQuantite());
                }
            }
        }

        $ligneFraisHorsForfait = new LigneFraisHorsForfait();
        $formFraisHF = $this->createForm(SaisirFicheFraisHorsForfaitType::class, $ligneFraisHorsForfait);
        $formFraisHF->handleRequest($request);

        if ($formFraisHF->isSubmitted() && $formFraisHF->isValid()) {

            $ligneFraisHorsForfait->setFicheFrais($ficheMoisUser);
            $ligneFraisHorsForfait->setDate($formFraisHF->get('date')->getData());
            $ligneFraisHorsForfait->setLibelle($formFraisHF->get('libelle')->getData());
            $ligneFraisHorsForfait->setMontant($formFraisHF->get('montant')->getData());
            $entityManager->persist($ligneFraisHorsForfait);
            $entityManager->flush();

            return $this->redirectToRoute('app_saisir_fiche_frais', [], Response::HTTP_SEE_OTHER);
        }
        $selectedFiche = null;
        $user = $this->getUser();

            $selectedFiche = $doctrine->getRepository(FicheFrais::class)->findOneBy(['mois' => $moisActuel, 'user' => $user]);


        return $this->render('saisir_fiche_frais/index.html.twig', [
            'controller_name' => 'SaisirFicheFraisController',
            'formFraisHF' => $formFraisHF,
            'formFraisF' =>$formFraisF,
            'selectedFiche' => $selectedFiche,
        ]);
    }
}

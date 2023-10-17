<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/import')]
class DataImportController extends AbstractController
{
    #[Route('/user', name: 'app_data_import_user')]
    public function user(UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine): Response
    {

        $usersjson = file_get_contents('./import/gsbfraisusers.json');
        $users = json_decode($usersjson);

        foreach ($users as $user) {
            $newUser = new User();
            $newUser->setNom($user->nom);
            $newUser->setPrenom($user->prenom);


            $hashedPassword = $passwordHasher->hashPassword($newUser, $user->mdp);
            $newUser->setPassword($hashedPassword);

            // Mettre en minuscule
            $prenomMinuscules = strtolower($user->prenom);
            $nomMinuscules = strtolower($user->nom);
            // Retirer caractères spéciaux
            $prenomSansCaracteresSpeciaux = preg_replace('/[^a-z]/', '', $prenomMinuscules);
            $nomSansCaracteresSpeciaux = preg_replace('/[^a-z]/', '', $nomMinuscules);
            // Créer email en faisant prenom.nom@gsb.fr
            $email = $prenomSansCaracteresSpeciaux . '.' . $nomSansCaracteresSpeciaux . '@gsb.fr';
            $newUser->setEmail($email);
            $newUser->setOldId($user->id);

            $newUser->setAdresse($user->adresse);
            $newUser->setCp($user->cp);
            $newUser->setVille($user->ville);
            $newUser->setDateEmbauche(new \DateTime(  $user->dateEmbauche));

            $doctrine->getManager()->persist($newUser);
            $doctrine->getManager()->flush();
        }




        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }


    #[Route('/fiche', name: 'app_data_import_fiche')]
    public function fiche(UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine): Response
    {

        $fichesFraisJson = file_get_contents('./import/fichefrais.json');
        $fichesFrais = json_decode($fichesFraisJson, true); // Ajout de true pour obtenir un tableau associatif

        foreach ($fichesFrais as $ficheFrais) {
            $newFicheFrais = new FicheFrais();


            $newFicheFrais->setMois($ficheFrais['mois']);
            $newFicheFrais->setNbJustificatifs($ficheFrais['nbJustificatifs']);
            $newFicheFrais->setMontantValid($ficheFrais['montantValide']);
            $newFicheFrais->setDateModif(new \DateTime($ficheFrais['dateModif']));




            $user = $doctrine->getRepository(User::class)->findOneBy(['old_id' => $ficheFrais['idVisiteur']]);
            switch ($ficheFrais['idEtat']){
                case 'CL':
                    $etat = $doctrine->getRepository(Etat::class)->find(1);
                    break;
                case 'CR':
                    $etat = $doctrine->getRepository(Etat::class)->find(2);
                    break;
                case 'RB':
                    $etat = $doctrine->getRepository(Etat::class)->find(3);
                    break;
                case 'VA':
                    $etat = $doctrine->getRepository(Etat::class)->find(4);
                    break;
            }

            $newFicheFrais->setUser($user);
            $newFicheFrais->setEtat($etat);

            $doctrine->getManager()->persist($newFicheFrais);
        }

        $doctrine->getManager()->flush();

        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }


    #[Route('/fraisForfait', name: 'app_data_import_frais_forfait')]
    public function fraisForfait(UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine): Response
    {

        $fraisForfaitsjson = file_get_contents('./import/fraisforfait.json');
        $fraisForfaits = json_decode($fraisForfaitsjson);

        foreach ($fraisForfaits as $fraisForfait) {
            $newFraisForfait = new FraisForfait();
            $newFraisForfait->setLibelle($fraisForfait->libelle);
            $newFraisForfait->setMontant($fraisForfait->montant);


            $doctrine->getManager()->persist($newFraisForfait);
            $doctrine->getManager()->flush();
        }




        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }

    #[Route('/forfait', name: 'app_data_import_ligne_forfait')]
    public function forfait(UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine): Response
    {

        $ligneForfaitsjson = file_get_contents('./import/lignefraisforfait.json');
        $ligneForfaits = json_decode($ligneForfaitsjson, true);

        foreach ($ligneForfaits as $ligneForfait) {
            $newLigneForfait = new LigneFraisForfait();
            $newLigneForfait->setQuantite($ligneForfait['quantite']);
            $user = $doctrine->getRepository(User::class)->findOneBy(['old_id' => $ligneForfait['idVisiteur']]);
            $fiche = $doctrine->getRepository(FicheFrais::class)->findOneBy(['user' => $user, 'mois' => $ligneForfait['mois']]);
            $newLigneForfait->setFicheFrais($ligneForfait($fiche));

            $forfait = $doctrine->getRepository(FraisForfait::class)->findOneBy(['frais_forfait_id' => $ligneForfait['idFraisForfait']]);

            $doctrine->getManager()->persist($newLigneForfait);
            $doctrine->getManager()->flush();
        }




        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }

}

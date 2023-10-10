<?php

namespace App\Controller;

use App\Entity\FicheFrais;
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
            $ficheFrais = new FicheFrais();


            $ficheFrais->setMois($ficheFrais['mois']);
            $ficheFrais->setNbJustificatifs($ficheFrais['nbJustificatifs']);
            $ficheFrais->setMontantValid($ficheFrais['montantValide']);
            $ficheFrais->setDateModif(new \DateTime($ficheFrais['dateModif']));


            $user = $this->getUserById($ficheFrais['idVisiteur']);
            $etat = $this->getEtatById($ficheFrais['idEtat']);

            $ficheFrais->setUser($user);
            $ficheFrais->setEtat($etat);

            $doctrine->getManager()->persist($ficheFrais);
        }

        $doctrine->getManager()->flush();

        return $this->render('import_fiche_frais/index.html.twig', [
            'controller_name' => 'ImportFicheFraisController',
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class NewUserController extends AbstractController
{
    #[Route('/new/user', name: 'app_new_user')]
    public function index(UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine): Response
{
    $unUser = new User();
    $unUser->setEmail('admin@lycee-faure.fr');
    $unUser->setRoles(['ROLE_COMPTABLE']);
    $plaintextPassword = "1234";

    // hash the password (based on the security.yaml config for the $user class)
    $hashedPassword = $passwordHasher->hashPassword(
        $unUser,
        $plaintextPassword
    );
    $unUser->setPassword($hashedPassword);

    $entityManager = $doctrine->getManager();
    $entityManager->persist($unUser);
    $entityManager->flush();

    return $this->render('new_user/index.html.twig', [
        'controller_name' => 'NewUserController',
    ]);
}
}

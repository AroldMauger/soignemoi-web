<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\UsersType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersController extends AbstractController
{
    #[Route('/signup', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            // Assurez-vous que les rôles sont bien définis
            if (empty($user->getRoles())) {
                $user->setRoles(['ROLE_USER']);  // Définir les rôles par défaut si aucun n'est défini
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_success');
        }

        return $this->render('pages/signup-form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/signup-success', name: 'user_success')]
    public function signupSuccess(): Response
    {
        return $this->render('pages/signup-success.html.twig');
    }


}

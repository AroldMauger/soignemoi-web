<?php

namespace App\Controller;

use App\Entity\Doctors;
use App\Entity\Users;
use App\Form\DoctorsType;
use App\Form\UsersType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin-dashboard', name: 'admin_dashboard')]
    public function adminDashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('pages/admin-dashboard.html.twig');
    }
    #[Route('/add-doctor', name: 'doctor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $doctor = new Doctors();
        $form = $this->createForm(DoctorsType::class, $doctor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($doctor);
            $entityManager->flush();

            return $this->redirectToRoute('doctor_success');
        }


        return $this->render('pages/add-doctors.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/add-doctor-success', name: 'doctor_success')]
    public function signupSuccess(): Response
    {
        return $this->render('pages/add-doctors-success.html.twig');
    }

}
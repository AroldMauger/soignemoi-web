<?php

namespace App\Controller;

use App\Entity\Doctors;
use App\Entity\Stays;
use App\Form\DoctorsType;
use App\Form\StaysType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Form\NewAppointmentType;
use App\Repository\AppointmentsRepository;

class HomeController extends AbstractController {

    #[Route('/dashboard', name:"dashboard", methods: ['GET'])]
    public function dashboard()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render("pages/dashboard.html.twig");
    }



    #[Route('/add-stay', name: 'add-stay', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stay = new Stays();
        $form = $this->createForm(StaysType::class, $stay);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($stay);
            $entityManager->flush();

            return $this->redirectToRoute('stay_success');
        }

        return $this->render('pages/add-stay.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/add-stay-success', name: 'stay_success')]
    public function signupSuccess(): Response
    {
        return $this->render('pages/add-stay-success.html.twig');
    }
}
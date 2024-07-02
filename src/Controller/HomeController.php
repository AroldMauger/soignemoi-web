<?php

namespace App\Controller;

use App\Entity\Doctors;
use App\Entity\Stays;
use App\Form\DoctorsType;
use App\Form\StaysType;
use App\Repository\StaysRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class HomeController extends AbstractController {

    #[Route('/dashboard', name:"dashboard", methods: ['GET'])]
    public function dashboard(StaysRepository $staysRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Mettre à jour les statuts des séjours
        $staysRepository->updateStayStatuses();

        // Récupérer les séjours en cours
        $currentStays = $staysRepository->findCurrentStays();

        // Récupérer les séjours à venir
        $upcomingStays = $staysRepository->findUpcomingStays();

        return $this->render('pages/dashboard.html.twig', [
            'currentStays' => $currentStays,
            'upcomingStays' => $upcomingStays,
        ]);
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

    #[Route('/history', name:"history", methods: ['GET'])]
    public function history(StaysRepository $repo, Request $request)
    {
        $page = $request->get('page', 0);
        $limit = $request->get('limit', 5);
        $count = $repo->count(["status" => "terminé"]);
        $stays = $repo->findFinishedPaginated($page, $limit);
        $totalPages = (int) ceil($count/$limit);
        $previousPage = $page == 0? null:$page-1;
        $nextPage = ($page+1 == $totalPages || $page>$totalPages) ? null:$page+1;
        $firstPage = $page == 0? null: 0;
        $lastPage = $totalPages - 1;

        return $this->render("pages/history.html.twig", ["stays" => $stays, "firstPage" => $firstPage, "lastPage" => $lastPage, "previousPage" => $previousPage, "nextPage" => $nextPage, "page" => $page]);
    }
}
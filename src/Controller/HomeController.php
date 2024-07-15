<?php
namespace App\Controller;

use App\Entity\Stays;
use App\Repository\StaysRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HomeController extends AbstractController {

    #[Route('/dashboard', name:"dashboard", methods: ['GET'])]
    public function dashboard(StaysRepository $staysRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser(); // Récupérer l'utilisateur connecté

        $staysRepository->updateStayStatuses();

        $currentStays = $staysRepository->findCurrentStays($user);
        $upcomingStays = $staysRepository->findUpcomingStays($user);

        return $this->render('pages/dashboard.html.twig', [
            'currentStays' => $currentStays,
            'upcomingStays' => $upcomingStays,
        ]);
    }

    #[Route('/history', name:"history", methods: ['GET'])]
    public function history(StaysRepository $repo, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser(); // Récupérer l'utilisateur connecté

        $page = $request->get('page', 0);
        $limit = $request->get('limit', 5);
        $count = $repo->count(['status' => 'terminé', 'user' => $user]);
        $stays = $repo->findFinishedPaginated($user, $page, $limit);
        $totalPages = (int) ceil($count / $limit);
        $previousPage = $page == 0 ? null : $page - 1;
        $nextPage = ($page + 1 == $totalPages || $page > $totalPages) ? null : $page + 1;
        $firstPage = $page == 0 ? null : 0;
        $lastPage = $totalPages - 1;

        return $this->render("pages/history.html.twig", [
            'stays' => $stays,
            'firstPage' => $firstPage,
            'lastPage' => $lastPage,
            'previousPage' => $previousPage,
            'nextPage' => $nextPage,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }
}
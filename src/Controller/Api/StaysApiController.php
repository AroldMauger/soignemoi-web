<?php

namespace App\Controller\Api;

use App\Context\UserApiContext;
use App\Entity\Appointments;
use App\Entity\Stays;
use App\Repository\AppointmentsRepository;
use App\Repository\StaysRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/api/stays")]
class StaysApiController extends AbstractController
{
    #[Route("", methods: ["GET"])]
    public function getStays(UserApiContext $userApiContext, StaysRepository $repo, Request $request)
    {
        $selectedDate = $request->request->get('selected-date');

        if ($selectedDate) {
            $date = new DateTime($selectedDate);
            $stays = $repo->findByDate($date, "en cours");
        } else {
            $stays = $repo->findBy(['status' => 'en cours']);
        }
        $userApiContext ->getUser();

        return $this ->json($stays);
    }

    #[Route('/history', name:"api.history", methods: ['GET'])]
    public function history(StaysRepository $repo,
                            #[MapQueryParameter] int $page = 0,
                            #[MapQueryParameter(options: ["min_range" => 5, "max_range" => 50])] int $limit = 5

    )
    {
        $count = $repo->count(["status" => "terminé"]);
        $stays = $repo->findFinishedPaginated($page, $limit);
        $totalPages = (int) ceil($count/$limit);
        $previousPage = $page == 0? null:$page-1;
        $nextPage = ($page+1 == $totalPages || $page>$totalPages) ? null:$page+1;
        $firstPage = $page == 0? null: 0;
        $lastPage = $totalPages - 1;
        return $this->json(["appointments" => $stays, "firstPage" => $firstPage, "lastPage" => $lastPage, "previousPage" => $previousPage, "nextPage" => $nextPage]);
    }

    #[Route('/{id}', name:"api.finish", methods: ['PATCH'])]
    public function finish(#[MapEntity(id:"id")] Stays $stay, StaysRepository $repo)
    {
        $stay->setStatus("terminé");
        $repo->save($stay);
        return $this->json("", Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name:"api.delete", methods: ['DELETE'])]
    public function delete(
        #[MapEntity(id:"id")] Stays $stay,
        StaysRepository $repo,
    )
    {
        $repo->delete($stay);
        return $this->json("", Response::HTTP_NO_CONTENT);
    }
}
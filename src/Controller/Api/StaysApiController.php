<?php

namespace App\Controller\Api;

use App\Context\DoctorApiContext;
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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

#[Route("/api/stays")]
class StaysApiController extends AbstractController
{
    #[Route("", methods: ["GET"])]
    public function getStays(DoctorApiContext $userApiContext, StaysRepository $repo, Request $request, TokenStorageInterface $tokenStorage): Response
    {
        try {
            $token = $tokenStorage->getToken();
            if (!$token) {
                throw new AuthenticationException('Token not found');
            }

            $user = $token->getUser();
            // Vérifiez les autorisations de l'utilisateur ici
            // ...

            $selectedDate = $request->query->get('selected-date');
            if ($selectedDate) {
                try {
                    $date = new DateTime($selectedDate);
                    $stays = $repo->findCurrentStays();
                } catch (\Exception $e) {
                    return $this->json(['error' => 'Invalid date format'], Response::HTTP_BAD_REQUEST);
                }
            } else {
                $stays = $repo->findCurrentStays();
            }

            $userApiContext->getDoctors();

            return $this->json($stays, Response::HTTP_OK, [], ['groups' => 'stays']);
        } catch (AuthenticationException $e) {
            return new Response($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
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
<?php

namespace App\Controller\Api;

use App\Context\DoctorApiContext;
use App\Entity\Stays;
use App\Repository\StaysRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Serializer\SerializerInterface;

#[Route("")]
class StaysApiController extends AbstractController
{
    #[Route('/api/stays', name: 'api_stays', methods: ['GET'])]
    public function index(StaysRepository $staysRepository): JsonResponse
    {
        $stays = $staysRepository->findAll();

        // Convert entities to arrays
        $staysArray = array_map([$this, 'transformStayToArray'], $stays);

        return new JsonResponse($staysArray, JsonResponse::HTTP_OK);
    }

    private function transformStayToArray(Stays $stay): array
    {
        return [
            'id' => $stay->getId(),
            'entrydate' => $stay->getEntrydate()->format('Y-m-d\TH:i:sP'),
            'leavingdate' => $stay->getLeavingdate()->format('Y-m-d\TH:i:sP'),
            'speciality' => $stay->getSpeciality() ? [
                'id' => $stay->getSpeciality()->getId(),
                'name' => $stay->getSpeciality()->getName(),
                'code' => $stay->getSpeciality()->getCode()
            ] : null,
            'reason' => $stay->getReason() ? [
                'id' => $stay->getReason()->getId(),
                'name' => $stay->getReason()->getName()
            ] : null,
            'doctor' => $stay->getDoctor() ? [
                'id' => $stay->getDoctor()->getId(),
                'name' => $stay->getDoctor()->getLastname()
            ] : null,
            'slot' => $stay->getSlot() ? [
                'id' => $stay->getSlot()->getId(),
                'time' => $stay->getSlot()->getStarttime()
            ] : null,
            'user' => $stay->getUser() ? [
                'id' => $stay->getUser()->getId(),
                'username' => $stay->getUser()->getFirstname()
            ] : null,
            'status' => $stay->getStatus(),
            'opinions' => $stay->getOpinions()->map(function($opinion) {
                return [
                    'id' => $opinion->getId(),
                    'comment' => $opinion->getComment()
                ];
            })->toArray(),
            'prescriptions' => $stay->getPrescriptions()->map(function($prescription) {
                return [
                    'id' => $prescription->getId(),
                    'medicine' => $prescription->getMedicine()
                ];
            })->toArray()
        ];
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
        return $this->json(["stays" => $stays, "firstPage" => $firstPage, "lastPage" => $lastPage, "previousPage" => $previousPage, "nextPage" => $nextPage]);
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
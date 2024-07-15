<?php

namespace App\Controller\Api;

use App\Entity\Stays;
use App\Repository\StaysRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/stays", name: 'api_stays')]
class StaysApiController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(
        Request $request,
        StaysRepository $staysRepository
    ): JsonResponse {
        $doctorLastName = $request->query->get('doctorLastName');

        $stays = $doctorLastName
            ? $staysRepository->findNonTerminatedStaysByDoctorLastName($doctorLastName)
            : $staysRepository->findNonTerminatedStays();

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
                'firstname' => $stay->getUser()->getFirstname(),
                'lastname' => $stay->getUser()->getLastname(),
                'address' => $stay->getUser()->getAddress(),
                'email' => $stay->getUser()->getEmail()
            ] : null,
            'status' => $stay->getStatus(),
            'opinions' => $stay->getOpinions()->map(function($opinion) {
                return [
                    'id' => $opinion->getId(),
                    'comment' => $opinion->getDescription(),
                    'date' => $opinion->getDate()
                ];
            })->toArray(),
            'prescriptions' => $stay->getPrescriptions()->map(function($prescription) {
                return [
                    'id' => $prescription->getId(),
                    'medicines' => $prescription->getMedicines()->map(function($medicine) {
                        return [
                            'name' => $medicine->getName(),
                            'dosage' => $medicine->getDosage(),
                            'start_date' => $medicine->getStartDate(),
                            'end_date' => $medicine->getEndDate(),
                        ];
                    })->toArray()
                ];
            })->toArray()
        ];
    }

    #[Route('/{id}/finish', methods: ['PATCH'])]
    public function finishStay(
        #[MapEntity] Stays $stay,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $stay->setStatus('terminé');
        $entityManager->persist($stay);
        $entityManager->flush();

        return new JsonResponse(['status' => 'success'], JsonResponse::HTTP_OK);
    }

    #[Route('/{id}/finish', methods: ['OPTIONS'])]
    public function options(): JsonResponse {
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
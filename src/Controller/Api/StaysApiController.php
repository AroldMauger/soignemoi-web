<?php

namespace App\Controller\Api;

use App\Context\DoctorApiContext;
use App\Entity\Stays;
use App\Repository\StaysRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Serializer\SerializerInterface;

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
            ? $staysRepository->findByDoctorLastName($doctorLastName)
            : $staysRepository->findAll();

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
                'lastname' => $stay->getUser()->getLastname()
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

}
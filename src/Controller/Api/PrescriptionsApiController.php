<?php

namespace App\Controller\Api;

use App\DTO\NewPrescriptionDTO;
use App\Entity\Prescriptions;
use App\Repository\PrescriptionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/api/prescriptions")]
class PrescriptionsApiController extends AbstractController
{
    #[Route('/new_prescription', name:"api.new_prescription", methods: ["POST"])]
    public function newPrescription(PrescriptionsRepository $repo,
                                   #[MapRequestPayload]
                                   NewPrescriptionDTO $newPrescriptionDTO,
    )
    {
        $prescription = new Prescriptions();
        $prescription
           ->setStay($newPrescriptionDTO->stayId);
        $repo->save($prescription);
        return $this->json($prescription, Response::HTTP_CREATED);

    }


}
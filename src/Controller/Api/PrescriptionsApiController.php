<?php

namespace App\Controller\Api;

use App\DTO\ChangePrescriptionsDTO;
use App\DTO\EndDateRequestDTO;
use App\Entity\Prescriptions;
use App\Entity\Medicines;
use App\Entity\Stays;
use App\Repository\PrescriptionsRepository;
use App\Repository\MedicinesRepository;
use App\Repository\StaysRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PrescriptionsApiController extends AbstractController
{

    #[Route('/api/prescriptions/{stayId}', name: 'change_prescriptions', methods: ['PUT'])]
    public function changePrescriptions(
        #[MapEntity(id: 'stayId')] Stays $stay,
        EntityManagerInterface           $em,
        Request                          $request
    ): Response
    {
        $input = json_decode($request->getContent());

        /** @var Prescriptions $prescription */
        foreach ($stay->getPrescriptions()->toArray() as $prescription) {
            /** @var Medicines $medicine */
            foreach ($prescription->getMedicines()->toArray() as $medicine) {
                $prescription->removeMedicine($medicine);
                $em->remove($medicine);
            }

            $em->remove($prescription);
            $em->flush();
        }

        $stay->getPrescriptions()->clear();
        foreach ($input->prescriptions as $prescription) {
            $pres = new Prescriptions();
            $pres->setStay($stay);

            $medicine = new Medicines();
            $medicine->setName($prescription->name)
                ->setDosage($prescription->dosage)
                ->setEnddate(new \DateTimeImmutable($prescription->enddate))
                ->setStartdate(new \DateTimeImmutable($prescription->startdate))
                ->setPrescrition($pres);

            $pres->addMedicine($medicine);
            $stay->addPrescription($pres);
        }

        $em->persist($stay);
        $em->flush();

        return $this->json('', Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/prescriptions', name: 'add_prescription', methods: ['POST'])]
    public function addPrescription(Request $request, StaysRepository $staysRepository, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $stay = $staysRepository->find($data['stayId']);
        if (!$stay) {
            return $this->json(['error' => 'Stay not found'], 404);
        }

        $prescription = new Prescriptions();
        $prescription->setStay($stay);

        $em->persist($prescription);
        $em->flush();

        return $this->json([
            'id' => $prescription->getId(),
            'stayId' => $stay->getId()
        ]);
    }

    #[Route('/api/prescriptions/{stayId}', name: 'get_prescriptions', methods: ['GET'])]
    public function getPrescriptions(int $stayId, PrescriptionsRepository $prescriptionsRepository): JsonResponse
    {
        $prescriptions = $prescriptionsRepository->findBy(['stay' => $stayId]);

        $data = [];
        foreach ($prescriptions as $prescription) {
            $medicines = [];
            foreach ($prescription->getMedicines() as $medicine) {
                $medicines[] = [
                    'id' => $medicine->getId(),
                    'name' => $medicine->getName(),
                    'dosage' => $medicine->getDosage(),
                    'startdate' => $medicine->getStartdate()->format('Y-m-d'),
                    'enddate' => $medicine->getEnddate()->format('Y-m-d')
                ];
            }

            $data[] = [
                'id' => $prescription->getId(),
                'medicines' => $medicines
            ];
        }

        return $this->json($data);
    }

    #[Route('/api/prescriptions/{prescriptionId}/medicines', name: 'add_medicine', methods: ['POST'])]
    public function addMedicines(int $prescriptionId, Request $request, PrescriptionsRepository $prescriptionsRepository, EntityManagerInterface $em): JsonResponse
    {
        $prescription = $prescriptionsRepository->find($prescriptionId);
        if (!$prescription) {
            return $this->json(['error' => 'Prescription not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $medicine = new Medicines();
        $medicine->setName($data['name'])
            ->setDosage($data['dosage'])
            ->setStartdate(new \DateTime($data['startdate']))
            ->setEnddate(new \DateTime($data['enddate']))
            ->setPrescrition($prescription);

        $em->persist($medicine);
        $em->flush();

        return $this->json(['success' => true, 'medicine' => [
            'id' => $medicine->getId(),
            'name' => $medicine->getName(),
            'dosage' => $medicine->getDosage(),
            'startdate' => $medicine->getStartdate()->format('Y-m-d'),
            'enddate' => $medicine->getEnddate()->format('Y-m-d')
        ]]);
    }

    #[Route('/api/prescriptions/{prescriptionId}/medicines', name: 'get_medicines', methods: ['GET'])]
    public function getMedicines(int $prescriptionId, PrescriptionsRepository $prescriptionsRepository): JsonResponse
    {
        $prescription = $prescriptionsRepository->find($prescriptionId);
        if (!$prescription) {
            return $this->json(['error' => 'Prescription not found'], 404);
        }

        $medicines = [];
        foreach ($prescription->getMedicines() as $medicine) {
            $medicines[] = [
                'id' => $medicine->getId(),
                'name' => $medicine->getName(),
                'dosage' => $medicine->getDosage(),
                'startdate' => $medicine->getStartdate()->format('Y-m-d'),
                'enddate' => $medicine->getEnddate()->format('Y-m-d')
            ];
        }

        return $this->json($medicines);
    }

    #[Route('/api/medicines/{medicineId}/enddate', name: 'update_end_date', methods: ['PATCH'])]
    public function updateEndDate(
        int                    $medicineId,
        Request                $request,
        MedicinesRepository    $medicinesRepository,
        EntityManagerInterface $em,
        SerializerInterface    $serializer
    ): JsonResponse
    {
        $medicine = $medicinesRepository->find($medicineId);
        if (!$medicine) {
            return $this->json(['error' => 'Medicine not found'], 404);
        }

        $data = $serializer->deserialize($request->getContent(), EndDateRequestDTO::class, 'json');
        $newEndDate = \DateTimeImmutable::createFromFormat('Y-m-d', $data->enddate);
        if ($newEndDate === false) {
            return $this->json(['error' => 'Invalid date format'], 400);
        }

        $medicine->setEnddate($newEndDate);
        $em->flush();

        return $this->json([
            'id' => $medicine->getId(),
            'name' => $medicine->getName(),
            'dosage' => $medicine->getDosage(),
            'startdate' => $medicine->getStartdate()->format('Y-m-d'),
            'enddate' => $medicine->getEnddate()->format('Y-m-d')
        ]);
    }
}

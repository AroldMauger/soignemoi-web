<?php

namespace App\Controller\Api;

use App\DTO\NewOpinionDTO;
use App\DTO\OpinionResponseDTO;
use App\Entity\Opinions;
use App\Repository\DoctorsRepository;
use App\Repository\OpinionsRepository;
use App\Repository\StaysRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/api/opinions")]
class OpinionsApiController extends AbstractController
{
    #[Route('/new_opinion', name: "api.new_opinion", methods: ["POST"])]
    public function newOpinion(
        Request $request,
        OpinionsRepository $opinionsRepository,
        DoctorsRepository $doctorsRepository,
        StaysRepository $staysRepository,
        ValidatorInterface $validator,
        LoggerInterface $logger
    ): Response {
        $data = json_decode($request->getContent(), true);
        $logger->info('Received data: ' . json_encode($data));

        if (is_null($data)) {
            $logger->error('Invalid JSON data');
            return $this->json(['error' => 'Invalid JSON data'], Response::HTTP_BAD_REQUEST);
        }

        // Create the DTO from the request data
        try {
            $newOpinionDTO = new NewOpinionDTO(
                $data['doctorId'],
                $data['stayId'],
                $data['date'],
                $data['description']
            );
        } catch (\TypeError $e) {
            $logger->error('Error creating DTO: ' . $e->getMessage());
            return $this->json(['error' => 'Missing or invalid data'], Response::HTTP_BAD_REQUEST);
        }

        // Validate the DTO
        $errors = $validator->validate($newOpinionDTO);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            $logger->error('Validation failed: ' . $errorsString);
            return $this->json(['error' => $errorsString], Response::HTTP_BAD_REQUEST);
        }

        // Find related entities
        $doctor = $doctorsRepository->find($newOpinionDTO->getDoctorId());
        $stay = $staysRepository->find($newOpinionDTO->getStayId());

        if (!$doctor || !$stay) {
            $logger->error('Invalid doctor or stay ID');
            return $this->json(['error' => 'Invalid doctor or stay ID'], Response::HTTP_BAD_REQUEST);
        }

        // Create and save the new opinion
        try {
            $date = new \DateTimeImmutable($newOpinionDTO->getDate());
        } catch (\Exception $e) {
            $logger->error('Error parsing date: ' . $e->getMessage());
            return $this->json(['error' => 'Invalid date format'], Response::HTTP_BAD_REQUEST);
        }

        $opinion = new Opinions();
        $opinion
            ->setDoctor($doctor)
            ->setStay($stay)
            ->setDate($date)
            ->setDescription($newOpinionDTO->getDescription());

        $opinionsRepository->save($opinion);

        // Build the response DTO
        $responseDTO = new OpinionResponseDTO(
            $opinion->getId(),
            $opinion->getDoctor()->getId(),
            $opinion->getStay()->getId(),
            $opinion->getDate()->format('Y-m-d H:i:s'),
            $opinion->getDescription()
        );

        return $this->json($responseDTO, Response::HTTP_CREATED);
    }
}

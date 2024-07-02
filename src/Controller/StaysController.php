<?php

namespace App\Controller;

use App\Entity\Doctors;
use App\Entity\Slot;
use App\Entity\Stays;
use App\Form\StaysType;
use App\Repository\SlotRepository;
use App\Service\SpecialityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StaysController extends AbstractController
{
    private $specialityService;

    public function __construct(SpecialityService $specialityService)
    {
        $this->specialityService = $specialityService;
    }

    #[Route('/add-stay', name: 'add-stay', methods: ['GET', 'POST'])]
    public function newStay(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stay = new Stays();
        $specialities = $this->specialityService->getSpecialities();
        ksort($specialities); // Tri des spécialités par ordre alphabétique

        // Récupérer la spécialité depuis la requête GET
        $speciality = $request->query->get('speciality');
        $doctors = [];
        $reasons = [];

        // Si une spécialité est sélectionnée, charger les médecins et les raisons
        if ($speciality) {
            $doctorRepository = $entityManager->getRepository(Doctors::class);
            $doctors = $doctorRepository->findBy(['speciality' => $speciality]);

            $reasons = $this->specialityService->getReasonsBySpeciality($speciality);
        }

        // Créer le formulaire en passant les spécialités, médecins et raisons
        $form = $this->createForm(StaysType::class, $stay, [
            'specialities' => $specialities,
            'reasons' => $reasons,
            'doctors' => $doctors,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if ($request->request->get('extend') === 'no') {
                $data->setLeavingdate($data->getEntrydate());
            }

            $data->setUser($this->getUser()); // Ajoutez cette ligne pour lier l'utilisateur au séjour
            $data->setStatus('en cours');

            $entityManager->persist($data);
            $entityManager->flush();

            return $this->redirectToRoute('stay_success');
        }

        return $this->render('pages/add-stay.html.twig', [
            'form' => $form->createView(),
            'specialities' => $specialities,
            'doctors' => $doctors,
            'reasons' => $reasons,
        ]);
    }

    #[Route('/stay-success', name: 'stay_success')]
    public function staySuccess(): Response
    {
        return $this->render('pages/add-stay-success.html.twig');
    }

    #[Route('/stay-search', name: 'stay_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $entityManager): Response
    {
        $speciality = $request->query->get('speciality');
        $specialities = $this->specialityService->getSpecialities();
        ksort($specialities);

        $doctors = [];
        $reasons = [];

        if ($speciality) {
            $doctorRepository = $entityManager->getRepository(Doctors::class);
            $doctors = $doctorRepository->findBy(['speciality' => $speciality]);

            $reasons = $this->specialityService->getReasonsBySpeciality($speciality);
        }

        return $this->json([
            'doctors' => array_map(function (Doctors $doctor) {
                return [
                    'id' => $doctor->getId(),
                    'firstname' => $doctor->getFirstname(),
                    'lastname' => $doctor->getLastname(),
                ];
            }, $doctors),
            'reasons' => $reasons,
        ]);
    }

    #[Route('/get-availability', name: 'get_availability', methods: ['POST'])]
    public function getAvailability(Request $request, SlotRepository $slotRepository): JsonResponse
    {
        $doctorId = $request->request->get('doctor_id');
        $date = $request->request->get('date');

        if (!$doctorId || !$date) {
            return $this->json(['error' => 'Les paramètres doctor_id et date sont requis.'], 400);
        }

        try {
            // Convertir la date reçue en objet DateTime en supprimant les heures si présentes
            $date = new \DateTime($date . ' 00:00:00');
        } catch (\Exception $e) {
            return $this->json(['error' => 'Date invalide.'], 400);
        }

        $slots = $slotRepository->findAvailableSlots($doctorId, $date);

        $availableSlots = [];
        foreach ($slots as $slot) {
            if (!$slot->isBooked()) {
                $availableSlots[] = [
                    'id' => $slot->getId(),
                    'starttime' => $slot->getStarttime()->format('H:i'),
                    'endtime' => $slot->getEndtime()->format('H:i')
                ];
            }
        }

        if (empty($availableSlots)) {
            return $this->json(['message' => 'Aucun créneau disponible pour cette date.'], 404);
        }

        return $this->json(['slots' => $availableSlots]);
    }
}
